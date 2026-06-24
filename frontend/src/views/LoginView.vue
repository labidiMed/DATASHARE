<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const email = ref('')
const password = ref('')
const error = ref(null)
const loading = ref(false)

const auth = useAuthStore()
const router = useRouter()

async function submit() {
  error.value = null
  loading.value = true
  try {
    await auth.login({ email: email.value, password: password.value })
    router.push('/files')
  } catch (e) {
    error.value = e.response?.data?.message || 'Échec de la connexion'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="page">
    <div class="card">
      <h1>Connexion</h1>

      <form @submit.prevent="submit">
        <div class="field">
          <label for="email">Email</label>
          <input id="email" v-model="email" type="email" required />
        </div>

        <div class="field">
          <label for="password">Mot de passe</label>
          <input id="password" v-model="password" type="password" required />
        </div>

        <p v-if="error" class="callout callout-error">{{ error }}</p>

        <button class="btn btn-primary btn-block" :disabled="loading">
          {{ loading ? 'Connexion…' : 'Connexion' }}
        </button>
      </form>

      <p class="text-center muted switch">
        Pas encore de compte ?
        <RouterLink to="/register">Créer un compte</RouterLink>
      </p>
    </div>
  </div>
</template>

<style scoped>
.switch {
  margin-top: 1rem;
}
</style>
