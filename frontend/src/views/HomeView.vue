<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

function go() {
  router.push('/upload')
}
</script>

<template>
  <div class="page">
    <p class="hero-text">Tu veux partager un fichier ?</p>
    <button class="circle" aria-label="Partager un fichier" @click="go">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 16V4" />
        <path d="M8 8l4-4 4 4" />
        <path d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
      </svg>
    </button>
  </div>
</template>

<style scoped>
.hero-text {
  color: #fff;
  font-size: 1.4rem;
  font-weight: 500;
  margin-bottom: 1.5rem;
  text-align: center;
}

.circle {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: var(--dark);
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: var(--shadow);
  transition: transform 0.15s;
}

.circle:hover {
  transform: scale(1.05);
}
</style>
