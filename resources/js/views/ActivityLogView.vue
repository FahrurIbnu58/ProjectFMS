<template>
  <div>
    <h2 class="text-xl font-bold mb-4">Log Aktivitas</h2>
    <div class="card !p-0 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead><tr class="border-b border-slate-200 dark:border-slate-700"><th class="table-th">Waktu</th><th class="table-th">Pengguna</th><th class="table-th">Aksi</th><th class="table-th">Detail</th></tr></thead>
          <tbody>
            <tr v-if="loading"><td colspan="4" class="table-td text-center">Memuat…</td></tr>
            <tr v-else-if="!rows.length"><td colspan="4" class="table-td text-center text-slate-500">Belum ada log.</td></tr>
            <tr v-for="(l,i) in rows" :key="l?.id ?? i" class="border-b border-slate-100 dark:border-slate-700/60">
              <td class="table-td whitespace-nowrap">{{ fmtDate(l?.created_at ?? l?.waktu) }}</td>
              <td class="table-td">{{ l?.user?.name ?? l?.user?.email ?? l?.username ?? l?.causer ?? '-' }}</td>
              <td class="table-td"><span class="rounded bg-slate-200 dark:bg-slate-700 px-2 py-0.5 text-xs">{{ l?.action ?? l?.aksi ?? l?.event ?? l?.activity ?? '-' }}</span></td>
              <td class="table-td">{{ l?.description ?? l?.deskripsi ?? l?.detail ?? l?.file_name ?? '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <Pagination :page="page" :last-page="lastPage" @change="load($event)" />
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Pagination from '../components/Pagination.vue';
const rows = ref([]);
const loading = ref(true);
const page = ref(1);
const lastPage = ref(1);
function fmtDate(s) { if (!s) return '-'; try { return new Date(s).toLocaleString('id-ID'); } catch { return s; } }
async function load(p = 1) {
  loading.value = true; page.value = p;
  try {
    const { data } = await axios.get('/activity-logs', { params: { page: p } });
    rows.value = data?.data ?? (Array.isArray(data) ? data : []);
    lastPage.value = data?.last_page ?? data?.meta?.last_page ?? 1;
  } catch { rows.value = []; }
  finally { loading.value = false; }
}
onMounted(() => load(1));
</script>
