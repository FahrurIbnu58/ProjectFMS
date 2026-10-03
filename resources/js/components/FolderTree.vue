<template>
  <ul class="space-y-0.5 text-sm">
    <li v-for="n in nodes" :key="n?.id ?? n?.name">
      <div class="flex items-center gap-1 rounded px-2 py-1 hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer" @click="toggle(n)">
        <span class="w-4 text-slate-400">{{ hasChildren(n) ? (open[n.id] ? '▾' : '▸') : '•' }}</span>
        <span @click.stop="$emit('select', n)" class="truncate" :class="selectedId === n?.id ? 'font-semibold text-blue-600 dark:text-blue-400' : ''">📁 {{ n?.name ?? n?.nama ?? '-' }}</span>
      </div>
      <div v-if="open[n.id] && hasChildren(n)" class="ml-5 border-l border-slate-200 dark:border-slate-700 pl-1">
        <FolderTree :nodes="children(n)" :selected-id="selectedId" @select="$emit('select', $event)" />
      </div>
    </li>
  </ul>
</template>
<script setup>
import { reactive } from 'vue';
const props = defineProps({ nodes: { type: Array, default: () => [] }, selectedId: { type: [Number, String], default: null } });
defineEmits(['select']);
const open = reactive({});
function children(n) { return n?.children ?? n?.childs ?? n?.subfolders ?? []; }
function hasChildren(n) { return (children(n)?.length ?? 0) > 0; }
function toggle(n) { if (hasChildren(n)) open[n.id] = !open[n.id]; else $emitSafe(n); }
function $emitSafe() {}
</script>
