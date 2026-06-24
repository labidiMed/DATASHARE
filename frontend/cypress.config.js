import { defineConfig } from 'cypress'

export default defineConfig({
  e2e: {
    baseUrl: 'http://localhost:5173',
    supportFile: false,
    video: false,
    defaultCommandTimeout: 10000, // le hachage bcrypt rend /auth/register lent (~4s)
    env: {
      apiUrl: 'http://localhost:8000/api/v1',
    },
  },
})
