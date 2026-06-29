<script setup>
import { ref } from 'vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const file = ref(null)
const password = ref('')
const expiresInDays = ref(7)
const error = ref(null)
const loading = ref(false)
const result = ref(null)
const copied = ref(false)
const fileInput = ref(null)

function onFileChange(event) {
  file.value = event.target.files[0] || null
}

function formatSize(bytes) {
  if (bytes < 1024) return `${bytes} o`
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} Ko`
  return `${(bytes / 1024 / 1024).toFixed(1)} Mo`
}

async function submit() {
  if (!file.value) {
    error.value = 'Choisis un fichier'
    return
  }
  error.value = null
  loading.value = true

  const form = new FormData()
  form.append('file', file.value)
  form.append('expires_in_days', expiresInDays.value)
  if (password.value) form.append('password', password.value)

  try {
    const endpoint = auth.isAuthenticated ? '/files' : '/files/anonymous'
    const { data } = await api.post(endpoint, form)
    result.value = data.data
  } catch (e) {
    const errors = e.response?.data?.errors
    error.value = errors
      ? Object.values(errors)[0][0]
      : e.response?.data?.message || "Échec de l'envoi"
  } finally {
    loading.value = false
  }
}

async function copyLink() {
  await navigator.clipboard.writeText(result.value.download_url)
  copied.value = true
  setTimeout(() => (copied.value = false), 2000)
}
</script>

<template>
  <div class="page">
    <div class="card">
      <h1>Ajouter un fichier</h1>

      <!-- État succès -->
      <template v-if="result">
        <p class="callout callout-warning">
          Félicitations, ton fichier sera conservé pendant {{ expiresInDays }} jour(s).
        </p>

        <div class="field">
          <label>Lien de téléchargement</label>
          <input :value="result.download_url" readonly />
        </div>

        <button class="btn btn-primary btn-block" @click="copyLink">
          {{ copied ? 'Lien copié ✓' : 'Copier le lien' }}
        </button>
        <button class="btn btn-ghost btn-block" @click="result = null">
          Ajouter un autre fichier
        </button>
      </template>

      <!-- Formulaire -->
      <form v-else @submit.prevent="submit">
        <p v-if="!auth.isAuthenticated" class="callout callout-info">
          Vous n'êtes pas connecté : le fichier ne sera rattaché à aucun compte (pas d'historique).
          <RouterLink to="/register">Créer un compte</RouterLink> pour le retrouver plus tard.
        </p>

        <div class="field">
          <label for="file">Fichier</label>
          <input id="file" ref="fileInput" type="file" @change="onFileChange" />
          <p v-if="file" class="muted">{{ file.name }} — {{ formatSize(file.size) }}</p>
        </div>

        <div class="field">
          <label for="password">Mot de passe</label>
          <input id="password" v-model="password" type="password" placeholder="Optionnel" />
        </div>

        <div class="field">
          <label for="expiration">Expiration</label>
          <select id="expiration" v-model="expiresInDays">
            <option :value="1">Une journée</option>
            <option :value="3">3 jours</option>
            <option :value="7">7 jours</option>
          </select>
        </div>

        <p v-if="error" class="callout callout-error">{{ error }}</p>

        <button class="btn btn-primary btn-block" :disabled="loading">
          {{ loading ? 'Envoi…' : 'Télécharger' }}
        </button>
      </form>
    </div>
  </div>
</template>
