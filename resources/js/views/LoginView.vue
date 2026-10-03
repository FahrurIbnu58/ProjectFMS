<template>
  <div class="min-h-screen flex items-center justify-center p-4 bg-slate-100 dark:bg-slate-900">
    <div class="card w-full max-w-sm !p-6">
      <h1 class="text-xl font-bold text-blue-700 dark:text-blue-400">FMS</h1>
      <p class="text-sm text-slate-500 mb-4">Masuk ke Sistem Manajemen Berkas</p>
      <form @submit.prevent="submit" class="space-y-3">
        <div><label class="label">Email</label><input v-model="email" type="email" required class="input" placeholder="admin@example.com" /></div>
        <div><label class="label">Kata sandi</label><input v-model="password" type="password" required class="input" placeholder="••••••••" /></div>
        <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        <button class="btn-primary w-full" :disabled="loading">{{ loading ? 'Memproses…' : 'Masuk' }}</button>
      </form>
      <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
        <button class="btn-secondary" @click="fill('admin@example.com','password')">Demo Admin</button>
        <button class="btn-secondary" @click="fill('viewer@example.com','password')">Demo Viewer</button>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { login } from '../store/auth.js';
const router = useRouter();
const route = useRoute();
const email = ref('');
const password = ref('');
const error = ref('');
const loading = ref(false);
function fill(e, p) { email.value = e; password.value = p; }
async function submit() {
  loading.value = true; error.value = '';
  try {
    await login(email.value, password.value);
    router.push(route.query.redirect || '/');
  } catch (e) {
    error.value = e?.response?.data?.message ?? e.message ?? 'Gagal masuk. Periksa email & kata sandi.';
  } finally { loading.value = false; }
}
</script>
