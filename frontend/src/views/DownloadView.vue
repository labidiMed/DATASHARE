<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'

const route = useRoute()
const token = route.params.token

const meta = ref(null)
const status = ref('loading') // loading | ready | notfound | expired
const password = ref('')
const error = ref(null)
const downloading = ref(false)

function formatSize(bytes) {
  if (bytes < 1024) return `${bytes} o`
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} Ko`
  return `${(bytes / 1024 / 1024).toFixed(1)} Mo`
}

async function loadMeta() {
  try {
    const { data } = await api.get(`/download/${token}`)
    meta.value = data
    status.value = 'ready'
  } catch (e) {
    status.value = e.response?.status === 410 ? 'expired' : 'notfound'
  }
}

async function download() {
  error.value = null
  downloading.value = true
  try {
    const res = await api.post(
      `/download/${token}`,
      meta.value.is_protected ? { password: password.value } : {},
      { responseType: 'blob' },
    )
    const url = URL.createObjectURL(res.data)
    const a = document.createElement('a')
    a.href = url
    a.download = meta.value.original_name
    a.click()
    URL.revokeObjectURL(url)
  } catch (e) {
    const code = e.response?.status
    if (code === 401) error.value = 'Mot de passe requis ou incorrect'
    else if (code === 410) status.value = 'expired'
    else if (code === 404) status.value = 'notfound'
    else error.value = 'Échec du téléchargement'
  } finally {
    downloading.value = false
  }
}

onMounted(loadMeta)
</script>

<template>
  <div class="page">
    <div class="card">
      <h1>Télécharger un fichier</h1>

      <p v-if="status === 'loading'" class="muted text-center">Chargement…</p>

      <p v-else-if="status === 'notfound'" class="callout callout-error">
        Ce lien est invalide ou le fichier n'existe plus.
      </p>

      <p v-else-if="status === 'expired'" class="callout callout-warning">
        Ce lien a expiré, le fichier n'est plus disponible.
      </p>

      <template v-else>
        <div class="file">
          <div class="file-info">
            <span class="name">{{ meta.original_name }}</span>
            <span class="muted">{{ formatSize(meta.size_bytes) }}</span>
          </div>
        </div>

        <div v-if="meta.is_protected" class="field">
          <label for="password">Mot de passe</label>
          <input id="password" v-model="password" type="password" placeholder="Requis" />
        </div>

        <p v-if="error" class="callout callout-error">{{ error }}</p>

        <button class="btn btn-primary btn-block" :disabled="downloading" @click="download">
          {{ downloading ? 'Téléchargement…' : 'Télécharger' }}
        </button>
      </template>
    </div>
  </div>
</template>

<style scoped>
.file {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.85rem;
  border: 1px solid var(--border);
  border-radius: 10px;
  margin-bottom: 1rem;
}

.file-info {
  display: flex;
  flex-direction: column;
}

.name {
  font-weight: 500;
}
</style>
