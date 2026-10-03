<template>
  <div @dragover.prevent="drag=true" @dragleave="drag=false" @drop.prevent="onDrop"
    class="rounded-xl border-2 border-dashed p-6 text-center transition cursor-pointer"
    :class="drag ? 'border-blue-500 bg-blue-50 dark:bg-blue-950' : 'border-slate-300 dark:border-slate-600'"
    @click="$refs.input.click()">
    <input ref="input" type="file" multiple class="hidden" @change="onPick" />
    <p class="text-sm">Seret & letakkan berkas di sini, atau <span class="text-blue-600 font-medium">klik untuk memilih</span></p>
    <ul v-if="list.length" class="mt-3 text-left text-xs space-y-1">
      <li v-for="(f,i) in list" :key="i" class="flex justify-between gap-2"><span class="truncate">{{ f.name }}</span><span>{{ pct[i] ?? 0 }}%</span></li>
    </ul>
  </div>
</template>
<script setup>
import { ref } from 'vue';
const emit = defineEmits(['files-selected']);
const drag = ref(false);
const list = ref([]);
const pct = ref({});
function onDrop(e) { drag.value = false; add(e.dataTransfer?.files); }
function onPick(e) { add(e.target.files); e.target.value = ''; }
function add(files) {
  if (!files?.length) return;
  list.value = [...files];
  emit('files-selected', [...files]);
}
defineExpose({ setProgress: (i, v) => { pct.value = { ...pct.value, [i]: v }; }, reset: () => { list.value = []; pct.value = {}; } });
</script>
