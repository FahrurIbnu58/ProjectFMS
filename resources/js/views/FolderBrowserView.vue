<template>
  <div>
    <Breadcrumb :trail="trail" @navigate="goTrail" />
    <div class="mt-3 flex flex-col md:flex-row gap-2 md:items-center">
      <h2 class="text-xl font-bold flex-1">{{ currentName }}</h2>
      <div v-if="admin" class="flex gap-2">
        <button class="btn-secondary" @click="showFolderModal=true; folderForm={name:''}">+ Folder</button>
        <button class="btn-primary" @click="showUpload=true">Unggah</button>
      </div>
    </div>

    <div class="mt-3"><SearchBar :departments="departments" v-model="filter" @search="loadFiles(1)" /></div>

    <div class="mt-4 grid md:grid-cols-4 gap-4">
      <div class="card md:col-span-1">
        <p class="font-semibold text-sm mb-2">Struktur Folder</p>
        <FolderTree :nodes="tree" :selected-id="folderId" @select="openFolder($event.id)" />
      </div>
      <div class="md:col-span-3 space-y-4">
        <!-- Subfolders -->
        <div>
          <p class="text-sm font-semibold mb-2">Folder</p>
          <div v-if="subfolders.length" class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            <div v-for="f in subfolders" :key="f?.id" class="card !p-3 cursor-pointer hover:shadow flex items-center gap-2" @click="openFolder(f.id)">
              <span>📁</span>
              <span class="truncate text-sm font-medium">{{ f?.name ?? f?.nama ?? '-' }}</span>
              <span v-if="admin" class="ml-auto flex gap-1" @click.stop>
                <button class="text-xs text-blue-600" @click="editFolder(f)">Ubah</button>
                <button class="text-xs text-red-600" @click="delFolder(f)">Hapus</button>
              </span>
            </div>
          </div>
          <p v-else class="text-sm text-slate-500">Tidak ada subfolder.</p>
        </div>
        <!-- Files -->
        <div>
          <p class="text-sm font-semibold mb-2">Berkas</p>
          <div v-if="filesLoading" class="text-sm text-slate-500">Memuat…</div>
          <div v-else-if="!files.length" class="text-sm text-slate-500">Tidak ada berkas.</div>
          <div v-else class="grid sm:grid-cols-2 gap-2">
            <FileCard v-for="f in files" :key="f?.id" :file="f" @open="$router.push(`/files/${f.id}`)" @preview="previewFile=f; showPreview=true" @download="download(f)" />
          </div>
          <Pagination :page="page" :last-page="lastPage" @change="loadFiles($event)" />
        </div>
      </div>
    </div>

    <!-- Folder modal -->
    <Modal :show="showFolderModal" :title="folderForm.id ? 'Ubah Folder' : 'Folder Baru'" @close="showFolderModal=false">
      <label class="label">Nama folder</label>
      <input v-model="folderForm.name" class="input" placeholder="Nama folder" />
      <template #footer>
        <button class="btn-secondary" @click="showFolderModal=false">Batal</button>
        <button class="btn-primary" @click="saveFolder">Simpan</button>
      </template>
    </Modal>

    <!-- Upload modal -->
    <Modal :show="showUpload" title="Unggah Berkas" @close="showUpload=false">
      <label class="label">Judul</label>
      <input v-model="uploadTitle" class="input mb-2" placeholder="Judul berkas (opsional)" />
      <label class="label">Departemen</label>
      <select v-model="uploadDept" class="input mb-2"><option value="">- Pilih -</option><option v-for="d in departments" :key="d?.id" :value="d?.id">{{ d?.name ?? '-' }}</option></select>
      <DragDropUpload ref="uploader" @files-selected="pendingFiles=$event" />
      <template #footer>
        <button class="btn-secondary" @click="showUpload=false">Batal</button>
        <button class="btn-primary" :disabled="!pendingFiles.length || uploading" @click="doUpload">{{ uploading ? 'Mengunggah…' : 'Unggah' }}</button>
      </template>
    </Modal>

    <PreviewModal :show="showPreview" :file="previewFile" @close="showPreview=false" @download="download(previewFile)" />
  </div>
</template>
<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import Breadcrumb from '../components/Breadcrumb.vue';
import FolderTree from '../components/FolderTree.vue';
import FileCard from '../components/FileCard.vue';
import SearchBar from '../components/SearchBar.vue';
import DragDropUpload from '../components/DragDropUpload.vue';
import Modal from '../components/Modal.vue';
import Pagination from '../components/Pagination.vue';
import PreviewModal from '../components/PreviewModal.vue';
import { isAdmin } from '../store/auth.js';

const route = useRoute();
const router = useRouter();
const admin = isAdmin;
const folderId = computed(() => route.params.id ? Number(route.params.id) : null);
const tree = ref([]);
const subfolders = ref([]);
const trail = ref([{ id: null, name: 'Root' }]);
const currentName = ref('Semua Berkas');
const files = ref([]);
const filesLoading = ref(false);
const page = ref(1);
const lastPage = ref(1);
const filter = ref({ q: '', department_id: '' });
const departments = ref([]);
const showFolderModal = ref(false);
const folderForm = ref({ id: null, name: '' });
const showUpload = ref(false);
const pendingFiles = ref([]);
const uploadTitle = ref('');
const uploadDept = ref('');
const uploading = ref(false);
const uploader = ref(null);
const showPreview = ref(false);
const previewFile = ref(null);

function listOf(res) {
  const d = res?.data;
  if (Array.isArray(d)) return d;
  return d?.data ?? d?.items ?? d?.folders ?? [];
}
function paginateOf(res) {
  const d = res?.data;
  return { data: d?.data ?? d?.items ?? (Array.isArray(d) ? d : []), last: d?.last_page ?? d?.meta?.last_page ?? 1 };
}

async function loadTree() {
  try {
    const { data } = await axios.get('/folders-tree');
    tree.value = data?.data ?? (Array.isArray(data) ? data : []);
  } catch {
    try {
      const { data } = await axios.get('/folders');
      tree.value = listOf({ data });
    } catch { tree.value = []; }
  }
}
async function loadDepartments() {
  try {
    const { data } = await axios.get('/departments');
    departments.value = listOf({ data });
  } catch { departments.value = []; }
}
async function loadFolder() {
  subfolders.value = [];
  if (!folderId.value) {
    // root: list top-level folders
    try {
      const { data } = await axios.get('/folders', { params: { parent_id: '' } });
      const all = listOf({ data });
      subfolders.value = all.filter(f => !f?.parent_id && f?.parent_id !== 0 ? true : !f?.parent_id);
      if (!subfolders.value.length) subfolders.value = all.slice(0, 24);
    } catch { subfolders.value = []; }
    trail.value = [{ id: null, name: 'Root' }];
    currentName.value = 'Semua Berkas';
    return;
  }
  try {
    const { data } = await axios.get(`/folders/${folderId.value}`);
    const f = data?.data ?? data;
    currentName.value = f?.name ?? f?.nama ?? 'Folder';
    subfolders.value = f?.children ?? f?.subfolders ?? [];
    // build trail defensively
    const t = [{ id: null, name: 'Root' }];
    if (f?.parent) t.push({ id: f.parent.id, name: f.parent.name ?? '…' });
    t.push({ id: f?.id, name: currentName.value });
    trail.value = t;
  } catch {
    currentName.value = 'Folder';
  }
}
async function loadFiles(p = 1) {
  filesLoading.value = true;
  page.value = p;
  try {
    const params = { page: p, per_page: 12, folder_id: folderId.value ?? undefined, q: filter.value.q || undefined, search: filter.value.q || undefined, department_id: filter.value.department_id || undefined };
    const { data } = await axios.get('/files', { params });
    const pg = paginateOf({ data });
    files.value = pg.data;
    lastPage.value = pg.last;
  } catch { files.value = []; }
  finally { filesLoading.value = false; }
}
function openFolder(id) { router.push(id ? `/folders/${id}` : '/folders'); }
function goTrail(t) { openFolder(t?.id); }
function editFolder(f) { folderForm.value = { id: f.id, name: f?.name ?? f?.nama ?? '' }; showFolderModal.value = true; }
async function saveFolder() {
  const name = folderForm.value.name?.trim();
  if (!name) return;
  try {
    if (folderForm.value.id) await axios.put(`/folders/${folderForm.value.id}`, { name, nama: name });
    else await axios.post('/folders', { name, nama: name, parent_id: folderId.value });
    showFolderModal.value = false;
    folderForm.value = { id: null, name: '' };
    await loadTree(); await loadFolder();
  } catch (e) { alert(e?.response?.data?.message ?? 'Gagal menyimpan folder.'); }
}
async function delFolder(f) {
  if (!confirm(`Hapus folder "${f?.name ?? ''}"?`)) return;
  try { await axios.delete(`/folders/${f.id}`); await loadTree(); await loadFolder(); }
  catch (e) { alert(e?.response?.data?.message ?? 'Gagal menghapus folder.'); }
}
async function doUpload() {
  if (!pendingFiles.value.length) return;
  uploading.value = true;
  try {
    for (let i = 0; i < pendingFiles.value.length; i++) {
      const fd = new FormData();
      fd.append('file', pendingFiles.value[i]);
      if (uploadTitle.value) { fd.append('title', uploadTitle.value); fd.append('judul', uploadTitle.value); }
      if (folderId.value) fd.append('folder_id', folderId.value);
      if (uploadDept.value) fd.append('department_id', uploadDept.value);
      await axios.post('/files', fd, { headers: { 'Content-Type': 'multipart/form-data' }, onUploadProgress: (e) => uploader.value?.setProgress(i, Math.round((e.loaded * 100) / (e.total || 1))) });
    }
    showUpload.value = false;
    pendingFiles.value = []; uploadTitle.value = ''; uploadDept.value = '';
    uploader.value?.reset();
    await loadFiles(1);
  } catch (e) { alert(e?.response?.data?.message ?? 'Gagal mengunggah.'); }
  finally { uploading.value = false; }
}
async function download(f) {
  if (!f?.id) return;
  try {
    const res = await axios.get(`/files/${f.id}/download`, { responseType: 'blob' });
    const cd = res.headers?.['content-disposition'] ?? '';
    const m = cd.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
    const fname = (m?.[1]?.replace(/['"]/g, '')) || f?.nama_file || f?.name || `file-${f.id}`;
    const url = URL.createObjectURL(res.data);
    const a = document.createElement('a');
    a.href = url; a.download = fname; a.click();
    URL.revokeObjectURL(url);
  } catch { alert('Gagal mengunduh.'); }
}

watch(() => route.params.id, async () => { await loadFolder(); await loadFiles(1); });
onMounted(async () => { await Promise.all([loadTree(), loadDepartments()]); await loadFolder(); await loadFiles(1); });
</script>
