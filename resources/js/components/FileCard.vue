<template>
  <div class="card hover:shadow transition cursor-pointer" @click="$emit('open', file)">
    <div class="flex items-start justify-between gap-2">
      <div class="min-w-0">
        <p class="font-medium truncate" :title="title">{{ title }}</p>
        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ file?.nama_file ?? file?.original_name ?? file?.filename ?? '' }}</p>
      </div>
      <span class="text-xl shrink-0">{{ icon }}</span>
    </div>
    <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-between gap-2">
      <span class="truncate">{{ file?.department?.name ?? file?.departemen ?? '' }}</span>
      <span class="shrink-0">{{ size }}</span>
    </div>
    <div class="mt-3 flex gap-2" @click.stop>
      <button class="btn-secondary !px-2.5 !py-1 !text-xs" @click="$emit('preview', file)">Pratinjau</button>
      <button class="btn-secondary !px-2.5 !py-1 !text-xs" @click="$emit('download', file)">Unduh</button>
    </div>
  </div>
</template>
<script setup>
import { computed } from 'vue';
const props = defineProps({ file: { type: Object, default: () => ({}) } });
defineEmits(['open', 'preview', 'download']);
const title = computed(() => props.file?.title ?? props.file?.judul ?? props.file?.nama_file ?? props.file?.name ?? 'Tanpa judul');
const icon = computed(() => {
  const m = (props.file?.mime_type ?? props.file?.mime ?? '').toLowerCase();
  const n = (props.file?.nama_file ?? props.file?.name ?? '').toLowerCase();
  if (m.includes('pdf') || n.endsWith('.pdf')) return '📕';
  if (m.includes('image') || /\.(png|jpe?g|gif|webp|svg)$/.test(n)) return '🖼️';
  if (m.includes('word') || /\.docx?$/.test(n)) return '📘';
  if (m.includes('sheet') || m.includes('excel') || /\.xlsx?$/.test(n)) return '📗';
  return '📄';
});
const size = computed(() => {
  const b = props.file?.size ?? props.file?.ukuran ?? null;
  if (b == null) return '';
  const kb = Number(b) / 1024;
  if (kb < 1024) return `${kb.toFixed(1)} KB`;
  return `${(kb / 1024).toFixed(2)} MB`;
});
</script>
