<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()

async function logout() {
  await auth.logout()
  router.push('/login')
}
</script>

<template>
  <header class="header">
    <RouterLink to="/" class="logo">DataShare</RouterLink>

    <nav class="actions">
      <template v-if="auth.isAuthenticated">
        <RouterLink to="/upload" class="btn btn-dark">Ajouter des fichiers</RouterLink>
        <button class="btn btn-link logout" @click="logout">Déconnexion</button>
      </template>
      <RouterLink v-else to="/login" class="btn btn-dark">Se connecter</RouterLink>
    </nav>
  </header>
</template>

<style scoped>
.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.5rem;
}

.logo {
  font-weight: 700;
  font-size: 1.25rem;
  color: var(--text);
}

.actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logout {
  color: #fff;
}
</style>
