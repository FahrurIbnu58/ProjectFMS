<template>
  <nav class="flex items-center gap-1 text-sm flex-wrap" aria-label="Breadcrumb">
    <button v-for="(t, i) in trail" :key="i" @click="$emit('navigate', t)" class="flex items-center gap-1 hover:text-blue-600 dark:hover:text-blue-400" :class="i === trail.length - 1 ? 'font-semibold text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400'">
      <span v-if="i > 0" class="mx-1">/</span><span>{{ t?.name ?? t?.nama ?? 'Root' }}</span>
    </button>
    <span v-if="!trail?.length" class="text-slate-500">Root</span>
  </nav>
</template>
<script setup>
defineProps({ trail: { type: Array, default: () => [] } });
defineEmits(['navigate']);
</script>
