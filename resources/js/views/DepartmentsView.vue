<template>
  <div>
    <div class="flex items-center gap-2 mb-4">
      <h2 class="text-xl font-bold flex-1">Departemen</h2>
      <button class="btn-primary" @click="openAdd">+ Tambah</button>
    </div>
    <div class="card !p-0 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead><tr class="border-b border-slate-200 dark:border-slate-700"><th class="table-th">Nama</th><th class="table-th">Deskripsi</th><th class="table-th text-right">Aksi</th></tr></thead>
          <tbody>
            <tr v-if="loading"><td colspan="3" class="table-td text-center">Memuat…</td></tr>
            <tr v-else-if="!rows.length"><td colspan="3" class="table-td text-center text-slate-500">Belum ada data.</td></tr>
            <tr v-for="d in rows" :key="d?.id" class="border-b border-slate-100 dark:border-slate-700/60">
              <td class="table-td font-medium">{{ d?.name ?? d?.nama ?? '-' }}</td>
              <td class="table-td">{{ d?.description ?? d?.deskripsi ?? '-' }}</td>
              <td class="table-td text-right whitespace-nowrap">
                <button class="text-blue-600 text-sm mr-2" @click="openEdit(d)">Ubah</button>
                <button class="text-red-600 text-sm" @click="remove(d)">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <Pagination :page="page" :last-page="lastPage" @change="load($event)" />
    <Modal :show="show" :title="form.id ? 'Ubah Departemen' : 'Tambah Departemen'" @close="show=false">
      <label class="label">Nama</label>
      <input v-model="form.name" class="input mb-2" placeholder="Nama departemen" />
      <label class="label">Deskripsi</label>
      <textarea v-model="form.description" class="input" rows="3"></textarea>
      <template #footer>
        <button class="btn-secondary" @click="show=false">Batal</button>
        <button class="btn-primary" @click="save">Simpan</button>
      </template>
    </Modal>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Modal from '../components/Modal.vue';
import Pagination from '../components/Pagination.vue';
const rows = ref([]);
const loading = ref(true);
const page = ref(1);
const lastPage = ref(1);
const show = ref(false);
const form = ref({ id: null, name: '', description: '' });
async function load(p = 1) {
  loading.value = true; page.value = p;
  try {
    const { data } = await axios.get('/departments', { params: { page: p } });
    rows.value = data?.data ?? (Array.isArray(data) ? data : []);
    lastPage.value = data?.last_page ?? data?.meta?.last_page ?? 1;
  } catch { rows.value = []; }
  finally { loading.value = false; }
}
function openAdd() { form.value = { id: null, name: '', description: '' }; show.value = true; }
function openEdit(d) { form.value = { id: d.id, name: d?.name ?? d?.nama ?? '', description: d?.description ?? d?.deskripsi ?? '' }; show.value = true; }
async function save() {
  if (!form.value.name.trim()) return;
  const payload = { name: form.value.name, nama: form.value.name, description: form.value.description, deskripsi: form.value.description };
  try {
    if (form.value.id) await axios.put(`/departments/${form.value.id}`, payload);
    else await axios.post('/departments', payload);
    show.value = false; await load(page.value);
  } catch (e) { alert(e?.response?.data?.message ?? 'Gagal menyimpan.'); }
}
async function remove(d) {
  if (!confirm(`Hapus departemen "${d?.name ?? ''}"?`)) return;
  try { await axios.delete(`/departments/${d.id}`); await load(page.value); }
  catch (e) { alert(e?.response?.data?.message ?? 'Gagal menghapus.'); }
}
onMounted(() => load(1));
</script>
