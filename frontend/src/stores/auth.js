import { defineStore } from 'pinia'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || null,
    user: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
  },

  actions: {
    async register(payload) {
      const { data } = await api.post('/auth/register', payload)
      this.setSession(data)
    },

    async login(payload) {
      const { data } = await api.post('/auth/login', payload)
      this.setSession(data)
    },

    async fetchMe() {
      const { data } = await api.get('/auth/me')
      this.user = data
    },

    async logout() {
      try {
        await api.post('/auth/logout')
      } catch {
        // on déconnecte localement même si l'appel échoue
      }
      this.clear()
    },

    setSession(data) {
      this.token = data.access_token
      this.user = data.user
      localStorage.setItem('token', data.access_token)
    },

    clear() {
      this.token = null
      this.user = null
      localStorage.removeItem('token')
    },
  },
})
