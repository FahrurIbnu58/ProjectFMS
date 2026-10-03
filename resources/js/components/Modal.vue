<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" @click="$emit('close')"></div>
    <div class="relative card w-full max-w-md !p-0 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 font-semibold">{{ title }}</div>
      <div class="p-4"><slot /></div>
      <div v-if="$slots.footer" class="px-4 py-3 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2"><slot name="footer" /></div>
    </div>
  </div>
</template>
<script setup>
defineProps({ show: Boolean, title: { type: String, default: '' } });
defineEmits(['close']);
</script>
