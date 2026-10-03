<template>
  <div>
    <h2 class="text-xl font-bold mb-4">Dasbor</h2>
    <div v-if="error" class="card !border-red-300 text-sm text-red-600 mb-4">{{ error }}</div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
      <StatCard label="Total Folder" :value="totals.folders" />
      <StatCard label="Total Berkas" :value="totals.files" />
      <StatCard label="Departemen" :value="totals.departments" />
      <StatCard label="Total Unduhan" :value="totals.downloads ?? '-'" />
    </div>
    <div class="card mt-4 !p-0 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 font-semibold flex justify-between items-center">
        <span>Berkas Terbaru</span>
        <router-link to="/folders" class="text-sm text-blue-600 dark:text-blue-400">Lihat semua →</router-link>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead><tr class="border-b border-slate-200 dark:border-slate-700"><th class="table-th">Judul</th><th class="table-th">Folder</th><th class="table-th">Tanggal</th></tr></thead>
          <tbody>
            <tr v-if="loading"><td colspan="3" class="table-td text-center">Memuat…</td></tr>
            <tr v-else-if="!latest.length"><td colspan="3" class="table-td text-center text-slate-500">Belum ada berkas.</td></tr>
            <tr v-for="f in latest" :key="f?.id" class="border-b border-slate-100 dark:border-slate-700/60 hover:bg-slate-50 dark:hover:bg-slate-700/40 cursor-pointer" @click="$router.push(`/files/${f.id}`)">
              <td class="table-td font-medium">{{ f?.title ?? f?.nama_file ?? '-' }}</td>
              <td class="table-td">{{ f?.folder?.name ?? f?.folder?.nama ?? '-' }}</td>
              <td class="table-td">{{ fmtDate(f?.created_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import StatCard from '../components/StatCard.vue';
const totals = ref({ folders: '-', files: '-', departments: '-', downloads: '-' });
const latest = ref([]);
const loading = ref(true);
const error = ref('');
function fmtDate(s) { if (!s) return '-'; try { return new Date(s).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }); } catch { return s; } }
onMounted(async () => {
  try {
    const { data } = await axios.get('/dashboard');
    const d = data?.data ?? data ?? {};
    const t = d?.totals ?? d ?? {};
    totals.value = {
      folders: t?.total_folders ?? t?.folders ?? t?.folder_count ?? 0,
      files: t?.total_files ?? t?.files ?? t?.file_count ?? 0,
      departments: t?.total_departments ?? t?.departments ?? t?.department_count ?? 0,
      downloads: t?.total_downloads ?? t?.downloads ?? '-',
    };
    latest.value = d?.latest_files ?? d?.latest ?? d?.recent_files ?? [];
  } catch (e) { error.value = e?.response?.data?.message ?? 'Gagal memuat dasbor.'; }
  finally { loading.value = false; }
});
</script>
