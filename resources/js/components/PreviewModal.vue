<template>
  <Modal :show="show" :title="fileTitle" @close="$emit('close')">
    <div v-if="loading" class="text-sm text-slate-500">Memuat pratinjau…</div>
    <div v-else-if="error" class="text-sm text-red-600">{{ error }}</div>
    <template v-else>
      <img v-if="kind==='image'" :src="url" class="max-h-[60vh] w-full object-contain rounded" alt="pratinjau" />
      <iframe v-else-if="kind==='pdf'" :src="url" class="w-full h-[60vh] rounded border" title="pratinjau"></iframe>
      <p v-else class="text-sm">Pratinjau tidak tersedia untuk tipe ini. <button class="text-blue-600 underline" @click="$emit('download')">Unduh saja</button></p>
    </template>
    <template #footer>
      <button class="btn-secondary" @click="$emit('close')">Tutup</button>
      <button class="btn-primary" @click="$emit('download')">Unduh</button>
    </template>
  </Modal>
</template>
<script setup>
import { ref, watch, computed, onUnmounted } from 'vue';
import axios from 'axios';
import Modal from './Modal.vue';
const props = defineProps({ show: Boolean, file: { type: Object, default: null } });
defineEmits(['close', 'download']);
const url = ref(null);
const loading = ref(false);
const error = ref('');
const mime = computed(() => (props.file?.mime_type ?? props.file?.mime ?? '').toLowerCase());
const name = computed(() => (props.file?.nama_file ?? props.file?.name ?? '').toLowerCase());
const fileTitle = computed(() => props.file?.title ?? props.file?.nama_file ?? 'Pratinjau');
const kind = computed(() => {
  if (mime.value.includes('pdf') || name.value.endsWith('.pdf')) return 'pdf';
  if (mime.value.includes('image') || /\.(png|jpe?g|gif|webp|svg)$/.test(name.value)) return 'image';
  return 'other';
});
function revoke() { if (url.value) URL.revokeObjectURL(url.value); url.value = null; }
watch(() => [props.show, props.file?.id], async ([s]) => {
  revoke(); error.value = '';
  if (!s || !props.file?.id) return;
  if (kind.value === 'other') return;
  loading.value = true;
  try {
    const { data } = await axios.get(`/files/${props.file.id}/preview`, { responseType: 'blob' });
    url.value = URL.createObjectURL(data);
  } catch (e) {
    error.value = e?.response?.data?.message ?? 'Gagal memuat pratinjau.';
  } finally { loading.value = false; }
});
onUnmounted(revoke);
</script>
