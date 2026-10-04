<template>
  <!-- Menampilkan halaman tanpa Sidebar/Header jika halaman guest (Login, Register, Forgot Password) -->
  <div v-if="$route.meta.guest || ['/login', '/register', '/forgot-password'].includes($route.path)">
    <router-view />
  </div>
  <div v-else class="flex min-h-screen bg-slate-100 dark:bg-slate-900 text-slate-900 dark:text-slate-100">
    <!-- Sidebar desktop -->
    <aside
      class="hidden md:flex w-60 shrink-0 flex-col bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 min-h-screen sticky top-0 h-screen">
      <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
        <h1 class="text-lg font-bold text-blue-700 dark:text-blue-400">FMS</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400">Sistem Manajemen Berkas</p>
      </div>
      <nav class="flex-1 p-3 space-y-1 text-sm">
        <router-link to="/" class="nav" :class="{ active: $route.path === '/' }">Dasbor</router-link>
        <router-link to="/folders" class="nav"
          :class="{ active: $route.path.startsWith('/folders') || $route.path.startsWith('/files') }">Berkas /
          Folder</router-link>

        <!-- MENU PENGGUNA (PASTI TAMPIL UNTUK ADMIN) -->
        <router-link v-if="admin" to="/users" class="nav"
          :class="{ active: $route.path === '/users' }">Pengguna</router-link>

        <router-link v-if="admin" to="/departments" class="nav"
          :class="{ active: $route.path === '/departments' }">Departemen</router-link>
        <router-link v-if="admin" to="/logs" class="nav" :class="{ active: $route.path === '/logs' }">Log
          Aktivitas</router-link>
      </nav>
      <div class="p-3 border-t border-slate-200 dark:border-slate-700 text-xs text-slate-500 dark:text-slate-400">
        <p class="truncate">{{ auth.user?.name ?? auth.user?.email ?? '-' }}</p>
        <p class="capitalize">{{ roleDisplay }}</p>
      </div>
    </aside>

    <!-- Mobile drawer -->
    <div v-if="drawer" class="fixed inset-0 z-40 md:hidden">
      <div class="absolute inset-0 bg-black/40" @click="drawer = false"></div>
      <aside class="absolute left-0 top-0 h-full w-64 bg-white dark:bg-slate-800 p-4 space-y-1 shadow-xl">
        <div class="flex items-center justify-between mb-3">
          <h1 class="font-bold text-blue-700 dark:text-blue-400">FMS</h1>
          <button @click="drawer = false" class="btn-secondary !px-2 !py-1">✕</button>
        </div>
        <router-link @click="drawer = false" to="/" class="nav block">Dasbor</router-link>
        <router-link @click="drawer = false" to="/folders" class="nav block">Berkas / Folder</router-link>

        <!-- MENU PENGGUNA MOBILE -->
        <router-link v-if="admin" @click="drawer = false" to="/users" class="nav block">Pengguna</router-link>

        <router-link v-if="admin" @click="drawer = false" to="/departments" class="nav block">Departemen</router-link>
        <router-link v-if="admin" @click="drawer = false" to="/logs" class="nav block">Log Aktivitas</router-link>
      </aside>
    </div>

    <!-- Main Content -->
    <div class="flex-1 min-w-0 flex flex-col">
      <header
        class="sticky top-0 z-30 flex items-center gap-2 bg-white/90 dark:bg-slate-800/90 backdrop-blur border-b border-slate-200 dark:border-slate-700 px-4 py-2.5">
        <button class="md:hidden btn-secondary !px-2 !py-1" @click="drawer = true">☰</button>
        <div class="font-semibold md:hidden">FMS</div>
        <div class="flex-1"></div>
        <span class="hidden sm:inline text-xs text-slate-500 dark:text-slate-400">{{ auth.user?.email }}</span>
        <button @click="toggleDark" class="btn-secondary !px-2.5 !py-1.5"
          :title="isDark ? 'Mode terang' : 'Mode gelap'">{{ isDark ? '☀️' : '🌙' }}</button>
        <button @click="doLogout" class="btn-secondary !px-3 !py-1.5">Keluar</button>
      </header>
      <main class="p-4 md:p-6 max-w-6xl w-full mx-auto">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { auth, logout } from '../store/auth.js';

const router = useRouter();
const route = useRoute();
const drawer = ref(false);
const isDark = ref(document.documentElement.classList.contains('dark'));

// Pengecekan status Admin yang fleksibel & reaktif
const roleDisplay = computed(() => {
  const u = auth.user;
  if (!u) return '-';
  if (typeof u.role === 'string') return u.role;
  if (typeof u.role === 'object' && u.role !== null) return u.role.name || u.role.value || '-';
  return u.role_name || '-';
});

const admin = computed(() => {
  const r = String(roleDisplay.value).toLowerCase();
  return r.includes('admin') || r.includes('administrator');
});

function toggleDark() {
  const el = document.documentElement;
  el.classList.toggle('dark');
  isDark.value = el.classList.contains('dark');
  localStorage.setItem('fms_dark', isDark.value ? '1' : '0');
}

async function doLogout() {
  await logout();
  router.push('/login');
}
</script>

<style scoped>
.nav {
  display: block;
  padding: .55rem .8rem;
  border-radius: .6rem;
  color: inherit;
}

.nav:hover {
  background: rgba(100, 116, 139, .12);
}

.nav.active {
  background: #2563eb;
  color: #fff;
}
</style>