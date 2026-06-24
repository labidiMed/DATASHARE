<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

const files = ref([])
const tab = ref('active')
const loading = ref(true)

function formatSize(bytes) {
  if (bytes < 1024) return `${bytes} o`
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} Ko`
  return `${(bytes / 1024 / 1024).toFixed(1)} Mo`
}

function formatDate(iso) {
  return new Date(iso).toLocaleDateString('fr-FR')
}

const filtered = computed(() => {
  if (tab.value === 'active') return files.value.filter((f) => !f.is_expired)
  if (tab.value === 'expired') return files.value.filter((f) => f.is_expired)
  return files.value
})

function expirationLabel(file) {
  if (file.is_expired) return 'Expiré'
  const days = Math.ceil((new Date(file.expires_at) - new Date()) / 86400000)
  return days <= 1 ? 'Expire demain' : `Expire dans ${days} jours`
}

async function load() {
  loading.value = true
  try {
    const { data } = await api.get('/files')
    files.value = data.data
  } finally {
    loading.value = false
  }
}

async function remove(file) {
  if (!confirm(`Supprimer « ${file.original_name} » ?`)) return
  await api.delete(`/files/${file.id}`)
  files.value = files.value.filter((f) => f.id !== file.id)
}

onMounted(load)
</script>

<template>
  <div class="page space">
    <div class="panel">
      <h1>Mes fichiers</h1>

      <div class="tabs">
        <button :class="{ active: tab === 'all' }" @click="tab = 'all'">Tous</button>
        <button :class="{ active: tab === 'active' }" @click="tab = 'active'">Actifs</button>
        <button :class="{ active: tab === 'expired' }" @click="tab = 'expired'">Expiré</button>
      </div>

      <p v-if="loading" class="muted">Chargement…</p>
      <p v-else-if="filtered.length === 0" class="muted empty">Aucun fichier.</p>

      <ul v-else class="list">
        <li v-for="file in filtered" :key="file.id" class="row">
          <div class="info">
            <span class="name">{{ file.original_name }}</span>
            <span class="meta">
              {{ formatSize(file.size_bytes) }} · Envoyé le {{ formatDate(file.created_at) }}
            </span>
            <span class="exp" :class="{ expired: file.is_expired }">{{ expirationLabel(file) }}</span>
          </div>
          <div class="row-actions">
            <button class="btn btn-link danger" @click="remove(file)">Supprimer</button>
            <RouterLink
              v-if="!file.is_expired"
              :to="`/download/${file.download_token}`"
              class="btn btn-primary"
            >
              Accéder
            </RouterLink>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<style scoped>
.space {
  align-items: stretch;
}

.panel {
  background: var(--content-bg);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 2rem;
  width: 100%;
  max-width: 900px;
  margin: 0 auto;
}

.panel h1 {
  font-size: 1.4rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.list {
  list-style: none;
  padding: 0;
  margin-top: 1.25rem;
}

.row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 0.85rem 1rem;
  margin-bottom: 0.6rem;
}

.info {
  display: flex;
  flex-direction: column;
}

.name {
  font-weight: 500;
}

.meta {
  font-size: 0.78rem;
  color: var(--muted);
}

.exp {
  font-size: 0.8rem;
  color: var(--muted);
}

.exp.expired {
  color: #b91c1c;
}

.row-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.danger {
  color: #b91c1c;
}

.empty {
  margin-top: 1rem;
}
</style>
