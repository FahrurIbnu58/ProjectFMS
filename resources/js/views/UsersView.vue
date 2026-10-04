<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Kelola Pengguna</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Manajemen akun pengguna dan hak akses sistem</p>
      </div>
      <button @click="openModal()"
        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm transition flex items-center gap-2 shadow-lg shadow-blue-600/20">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah User Baru
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Total User</p>
        <p class="text-2xl font-bold mt-1 text-slate-800 dark:text-white">{{ users.length }}</p>
      </div>
      <div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Administrator</p>
        <p class="text-2xl font-bold mt-1 text-blue-600 dark:text-blue-400">{{ adminCount }}</p>
      </div>
      <div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Viewer / Staf</p>
        <p class="text-2xl font-bold mt-1 text-emerald-600 dark:text-emerald-400">{{ viewerCount }}</p>
      </div>
    </div>

    <!-- Filter & Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
      <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row gap-3 justify-between">
        <input v-model="search" type="text" placeholder="Cari nama atau email..."
          class="px-3 py-2 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead
            class="text-xs text-slate-500 uppercase bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
            <tr>
              <th class="px-4 py-3">Nama</th>
              <th class="px-4 py-3">Email</th>
              <th class="px-4 py-3">Role</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            <tr v-for="u in filteredUsers" :key="u.id"
              class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition">
              <td class="px-4 py-3 font-medium text-slate-800 dark:text-slate-200">{{ u.name }}</td>
              <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ u.email }}</td>
              <td class="px-4 py-3">
                <span
                  :class="u.role === 'administrator' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300'"
                  class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize">
                  {{ u.role }}
                </span>
              </td>
              <td class="px-4 py-3 text-right space-x-2">
                <button @click="openModal(u)"
                  class="text-blue-600 hover:text-blue-800 font-medium text-xs">Edit</button>
                <button @click="deleteUser(u.id)"
                  class="text-rose-600 hover:text-rose-800 font-medium text-xs">Hapus</button>
              </td>
            </tr>
            <tr v-if="filteredUsers.length === 0">
              <td colspan="4" class="px-4 py-8 text-center text-slate-500">Tidak ada data pengguna.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Form -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
      <div
        class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-4">
        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">
          {{ isEdit ? 'Edit User' : 'Tambah User Baru' }}
        </h3>

        <form @submit.prevent="saveUser" class="space-y-3">
          <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Nama Lengkap</label>
            <input v-model="form.name" type="text" required
              class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Email</label>
            <input v-model="form.email" type="email" required
              class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">
              Password {{ isEdit ? '(Kosongkan jika tidak diubah)' : '' }}
            </label>
            <input v-model="form.password" :required="!isEdit" type="password"
              class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Role</label>
              <select v-model="form.role" required
                class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="administrator">Administrator</option>
                <option value="viewer">Viewer</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Departemen</label>
              <select v-model="form.department_id"
                class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option :value="null">-- Pilih Departemen --</option>
                <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                  {{ dept.name }}
                </option>
              </select>
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-3">
            <button type="button" @click="showModal = false"
              class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700 rounded-lg">Batal</button>
            <button type="submit"
              class="px-4 py-2 text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-md">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const users = ref([]);
const departments = ref([]);
const search = ref('');
const showModal = ref(false);
const isEdit = ref(false);

const form = ref({
  id: null,
  name: '',
  email: '',
  password: '',
  role: 'viewer',
  department_id: null
});

const adminCount = computed(() => users.value.filter(u => u.role === 'administrator').length);
const viewerCount = computed(() => users.value.filter(u => u.role === 'viewer').length);

const filteredUsers = computed(() => {
  return users.value.filter(u => {
    const q = search.value.toLowerCase();
    return u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q);
  });
});

// PANGGUL API TANPA EKSPLISIT ALAMAT /api MANUALLY (Karena baseURL axios sudah /api)
async function fetchUsers() {
  try {
    const { data } = await axios.get('/users');
    users.value = data.data || data;
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal mengambil data user');
  }
}

async function fetchDepartments() {
  try {
    const { data } = await axios.get('/departments');
    departments.value = data.data || data;
  } catch (err) {
    console.error('Gagal memuat departemen:', err);
  }
}

function openModal(user = null) {
  if (user) {
    isEdit.value = true;
    form.value = {
      id: user.id,
      name: user.name,
      email: user.email,
      password: '',
      role: user.role || 'viewer',
      department_id: user.department_id || null
    };
  } else {
    isEdit.value = false;
    form.value = {
      id: null,
      name: '',
      email: '',
      password: '',
      role: 'viewer',
      department_id: null
    };
  }
  showModal.value = true;
}

async function saveUser() {
  try {
    if (isEdit.value) {
      await axios.put(`/users/${form.value.id}`, form.value);
    } else {
      await axios.post('/users', form.value);
    }
    showModal.value = false;
    fetchUsers();
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal menyimpan data user');
  }
}

async function deleteUser(id) {
  if (!confirm('Apakah Anda yakin ingin menghapus user ini?')) return;
  try {
    await axios.delete(`/users/${id}`);
    fetchUsers();
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal menghapus user');
  }
}

onMounted(() => {
  fetchUsers();
  fetchDepartments();
});
</script>