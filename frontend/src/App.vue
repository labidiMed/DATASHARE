<script setup>
import { RouterView } from 'vue-router'
import AppHeader from '@/components/AppHeader.vue'
</script>

<template>
  <div class="app-bg">
    <AppHeader />
    <RouterView />
    <footer class="footer">Copyright DataShare® 2026</footer>
  </div>
</template>

<style scoped>
.footer {
  text-align: center;
  padding: 1rem;
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.85);
}
</style>
