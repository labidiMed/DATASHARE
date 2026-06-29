<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const email = ref('')
const password = ref('')
const confirm = ref('')
const error = ref(null)
const loading = ref(false)

const auth = useAuthStore()
const router = useRouter()

async function submit() {
  error.value = null

  if (password.value !== confirm.value) {
    error.value = 'Les mots de passe ne correspondent pas'
    return
  }

  loading.value = true
  try {
    await auth.register({ email: email.value, password: password.value })
    router.push('/files')
  } catch (e) {
    const errors = e.response?.data?.errors
    error.value = errors
      ? Object.values(errors)[0][0]
      : e.response?.data?.message || 'Échec de la création du compte'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="page">
    <div class="card">
      <h1>Créer un compte</h1>

      <form @submit.prevent="submit">
        <div class="field">
          <label for="email">Email</label>
          <input id="email" v-model="email" type="email" required />
        </div>

        <div class="field">
          <label for="password">Mot de passe</label>
          <input id="password" v-model="password" type="password" minlength="8" required />
        </div>

        <div class="field">
          <label for="confirm">Vérification du mot de passe</label>
          <input id="confirm" v-model="confirm" type="password" minlength="8" required />
        </div>

        <p v-if="error" class="callout callout-error">{{ error }}</p>

        <button class="btn btn-primary btn-block" :disabled="loading">
          {{ loading ? 'Création…' : 'Créer un compte' }}
        </button>
      </form>

      <p class="text-center muted switch">
        Tu as déjà un compte ?
        <RouterLink to="/login">Connexion</RouterLink>
      </p>
    </div>
  </div>
</template>

<style scoped>
.switch {
  margin-top: 1rem;
}
</style>
