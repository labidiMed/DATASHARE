import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
})

// Ajoute le token JWT à chaque requête s'il existe
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Si le serveur répond 401, le token n'est plus valide : on déconnecte
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      const path = window.location.pathname
      if (!path.startsWith('/login') && !path.startsWith('/download')) {
        window.location.assign('/login')
      }
    }
    return Promise.reject(error)
  },
)

export default api
