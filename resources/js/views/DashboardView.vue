<template>
  <div class="space-y-8">
    <!-- JIKA ROLE BUKAN ADMINISTRATOR -->
    <div v-if="!isAdmin" class="space-y-6">
      <!-- Greeting Banner Dynamic -->
      <div
        class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl p-6 md:p-8 text-white shadow-xl shadow-blue-500/10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <span class="bg-white/20 text-xs px-3 py-1 rounded-full backdrop-blur-md font-medium">
            Departemen {{ userDepartment }}
          </span>
          <h1 class="text-2xl md:text-3xl font-bold mt-2">
            Selamat datang kembali, {{ userName }}! 👋
          </h1>
          <p class="text-blue-100 text-sm mt-1">
            Kelola dan temukan berkas departemen Anda dengan mudah di sini.
          </p>
        </div>
        <router-link to="/folders"
          class="bg-white text-blue-600 px-5 py-2.5 rounded-xl font-semibold shadow-lg hover:bg-blue-50 transition text-sm">
          + Temukan Berkas
        </router-link>
      </div>

      <!-- Quick Stats User Dynamic -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div
          class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
          <p class="text-xs text-slate-400 font-semibold uppercase">Berkas Saya</p>
          <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ userStats.my_files }}</p>
        </div>

        <div
          class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
          <p class="text-xs text-slate-400 font-semibold uppercase">Total Diunduh</p>
          <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ userStats.total_downloads }} x</p>
        </div>

        <div
          class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
          <p class="text-xs text-slate-400 font-semibold uppercase">Penyimpanan Terpakai</p>
          <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ userStats.storage_used }}</p>
        </div>
      </div>
    </div>

    <!-- JIKA ROLE ADALAH ADMINISTRATOR -->
    <div v-else class="space-y-6">
      <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Dasbor Ringkasan Sistem</h1>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80">
          <p class="text-xs text-slate-400 font-semibold uppercase">Total Folder</p>
          <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ adminStats.total_folders }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80">
          <p class="text-xs text-slate-400 font-semibold uppercase">Total Berkas</p>
          <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ adminStats.total_files }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80">
          <p class="text-xs text-slate-400 font-semibold uppercase">Departemen</p>
          <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ adminStats.total_departments }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { auth, isAdmin } from '../store/auth.js';

const userStats = ref({
  my_files: 0,
  total_downloads: 0,
  storage_used: '0 KB'
});

const adminStats = ref({
  total_folders: 0,
  total_files: 0,
  total_departments: 0
});

// Ambil nama user terautentikasi
const userName = computed(() => auth.user?.name || 'Pengguna');

// Ambil nama departemen user
const userDepartment = computed(() => {
  const dept = auth.user?.department;
  if (typeof dept === 'object' && dept !== null) return dept.name || 'Umum';
  return dept || auth.user?.department_name || 'Umum';
});

async function fetchDashboard() {
  try {
    const { data } = await axios.get('/dashboard');
    const res = data.data || data;

    if (isAdmin.value) {
      adminStats.value = {
        total_folders: res.total_folders || 0,
        total_files: res.total_files || 0,
        total_departments: res.total_departments || 0
      };
    } else {
      userStats.value = {
        my_files: res.my_files || 0,
        total_downloads: res.total_downloads || 0,
        storage_used: res.storage_used || '0 KB'
      };
    }
  } catch (err) {
    console.error('Gagal mengambil data dasbor:', err);
  }
}

onMounted(() => {
  fetchDashboard();
});
</script>