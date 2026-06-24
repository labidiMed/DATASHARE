import http from 'k6/http'
import { check, sleep } from 'k6'

// Test de charge sur l'endpoint public de métadonnées (lecture, chemin critique).
export const options = {
  vus: 10, // 10 utilisateurs virtuels simultanés
  duration: '15s',
  thresholds: {
    http_req_duration: ['p(95)<300'], // 95% des requêtes sous 300 ms
    http_req_failed: ['rate<0.01'], // moins de 1% d'erreurs
  },
}

export default function () {
  const res = http.get(`${__ENV.BASE_URL}/download/${__ENV.TOKEN}`)
  check(res, { 'status 200': (r) => r.status === 200 })
  sleep(1)
}
