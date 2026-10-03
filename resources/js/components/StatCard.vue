<template>
  <div class="card">
    <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ label }}</p>
    <p class="mt-1 text-2xl font-bold">{{ value }}</p>
  </div>
</template>
<script setup>
defineProps({ label: String, value: { type: [Number, String], default: '-' } });
</script>
