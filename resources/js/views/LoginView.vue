<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-900 p-4">
    <div class="bg-slate-800 border border-slate-700/60 rounded-2xl w-full max-w-md p-6 sm:p-8 shadow-2xl space-y-6">
      <!-- Title -->
      <div>
        <h2 class="text-2xl font-bold text-white tracking-tight">FMS</h2>
        <p class="text-slate-400 text-sm mt-1">Masuk ke Sistem Manajemen Berkas</p>
      </div>

      <!-- Alert Error -->
      <div v-if="errorMessage" class="p-3 bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs rounded-xl">
        {{ errorMessage }}
      </div>

      <!-- Form Login -->
      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email</label>
          <input v-model="email" type="email" required placeholder="admin@example.com"
            class="w-full px-3.5 py-2.5 bg-slate-900/60 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
        </div>

        <div>
          <div class="flex justify-between items-center mb-1.5">
            <label class="text-xs font-semibold text-slate-300">Kata sandi</label>
            <router-link to="/forgot-password" class="text-xs text-blue-400 hover:underline">
              Lupa password?
            </router-link>
          </div>
          <input v-model="password" type="password" required placeholder="••••••••"
            class="w-full px-3.5 py-2.5 bg-slate-900/60 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
        </div>

        <button type="submit" :disabled="loading"
          class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl text-sm transition shadow-lg shadow-blue-600/20 disabled:opacity-50">
          {{ loading ? 'Memproses...' : 'Masuk' }}
        </button>
      </form>

      <!-- Footer: Buat Akun Baru -->
      <div class="pt-4 border-t border-slate-700/60 text-center">
        <p class="text-slate-400 text-xs">
          Belum memiliki akun?
          <router-link to="/register" class="text-blue-400 font-semibold hover:underline ml-1">
            Buat Akun
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { login } from '../store/auth.js';

const email = ref('');
const password = ref('');
const errorMessage = ref('');
const loading = ref(false);
const router = useRouter();

async function handleLogin() {
  loading.value = true;
  errorMessage.value = '';
  try {
    await login(email.value, password.value);
    router.push('/');
  } catch (err) {
    errorMessage.value = err.response?.data?.message || err.message || 'Login gagal.';
  } finally {
    loading.value = false;
  }
}
</script>