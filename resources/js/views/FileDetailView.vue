<template>
  <div>
    <button class="text-sm text-slate-500 hover:text-blue-600 mb-2" @click="$router.back()">← Kembali</button>
    <div v-if="loading" class="text-sm text-slate-500">Memuat…</div>
    <div v-else-if="!file" class="card text-sm text-red-600">Berkas tidak ditemukan.</div>
    <div v-else class="card">
      <h2 class="text-lg font-bold">{{ file?.title ?? file?.judul ?? file?.nama_file ?? '-' }}</h2>
      <dl class="mt-3 grid sm:grid-cols-2 gap-2 text-sm">
        <div><dt class="text-slate-500">Nama file</dt><dd class="font-medium">{{ file?.nama_file ?? file?.name ?? '-' }}</dd></div>
        <div><dt class="text-slate-500">Folder</dt><dd class="font-medium">{{ file?.folder?.name ?? file?.folder?.nama ?? '-' }}</dd></div>
        <div><dt class="text-slate-500">Departemen</dt><dd class="font-medium">{{ file?.department?.name ?? file?.departemen ?? '-' }}</dd></div>
        <div><dt class="text-slate-500">Diunggah oleh</dt><dd class="font-medium">{{ file?.uploader?.name ?? file?.user?.name ?? file?.uploaded_by ?? '-' }}</dd></div>
        <div><dt class="text-slate-500">Tanggal unggah</dt><dd class="font-medium">{{ fmtDate(file?.created_at) }}</dd></div>
        <div><dt class="text-slate-500">Ukuran</dt><dd class="font-medium">{{ file?.size ?? file?.ukuran ?? '-' }}</dd></div>
      </dl>
      <div class="mt-4 flex flex-wrap gap-2">
        <button class="btn-primary" @click="showPreview=true">Pratinjau</button>
        <button class="btn-secondary" @click="download">Unduh</button>
        <template v-if="admin">
          <button class="btn-secondary" @click="showEdit=true">Ubah</button>
          <button class="btn-danger" @click="remove">Hapus</button>
        </template>
      </div>
    </div>
    <Modal :show="showEdit" title="Ubah Berkas" @close="showEdit=false">
      <label class="label">Judul</label>
      <input v-model="form.title" class="input mb-2" />
      <label class="label">Departemen</label>
      <select v-model="form.department_id" class="input"><option value="">- Pilih -</option><option v-for="d in departments" :key="d?.id" :value="d?.id">{{ d?.name ?? '-' }}</option></select>
      <template #footer>
        <button class="btn-secondary" @click="showEdit=false">Batal</button>
        <button class="btn-primary" @click="save">Simpan</button>
      </template>
    </Modal>
    <PreviewModal :show="showPreview" :file="file" @close="showPreview=false" @download="download" />
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import Modal from '../components/Modal.vue';
import PreviewModal from '../components/PreviewModal.vue';
import { isAdmin } from '../store/auth.js';
const route = useRoute();
const router = useRouter();
const admin = isAdmin;
const file = ref(null);
const loading = ref(true);
const showPreview = ref(false);
const showEdit = ref(false);
const form = ref({ title: '', department_id: '' });
const departments = ref([]);
function fmtDate(s) { if (!s) return '-'; try { return new Date(s).toLocaleString('id-ID'); } catch { return s; } }
async function load() {
  loading.value = true;
  try {
    const { data } = await axios.get(`/files/${route.params.id}`);
    file.value = data?.data ?? data;
    form.value = { title: file.value?.title ?? file.value?.judul ?? '', department_id: file.value?.department_id ?? file.value?.department?.id ?? '' };
    const d = await axios.get('/departments').catch(() => null);
    const dd = d?.data?.data ?? d?.data?.data ?? [];
    departments.value = Array.isArray(dd) ? dd : (d?.data?.data ?? []);
  } catch { file.value = null; }
  finally { loading.value = false; }
}
async function download() {
  try {
    const res = await axios.get(`/files/${route.params.id}/download`, { responseType: 'blob' });
    const url = URL.createObjectURL(res.data);
    const a = document.createElement('a');
    a.href = url; a.download = file.value?.nama_file ?? `file-${route.params.id}`; a.click();
    URL.revokeObjectURL(url);
  } catch { alert('Gagal mengunduh.'); }
}
async function save() {
  try {
    await axios.put(`/files/${route.params.id}`, { title: form.value.title, judul: form.value.title, department_id: form.value.department_id || null });
    showEdit.value = false; await load();
  } catch (e) { alert(e?.response?.data?.message ?? 'Gagal menyimpan.'); }
}
async function remove() {
  if (!confirm('Hapus berkas ini?')) return;
  try { await axios.delete(`/files/${route.params.id}`); router.push('/folders'); }
  catch (e) { alert(e?.response?.data?.message ?? 'Gagal menghapus.'); }
}
onMounted(load);
</script>
