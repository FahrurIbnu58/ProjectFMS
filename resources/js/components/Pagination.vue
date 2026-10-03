<template>
  <div v-if="lastPage > 1" class="flex items-center justify-center gap-1 mt-4 text-sm">
    <button class="btn-secondary !px-2.5 !py-1" :disabled="page<=1" @click="$emit('change', page-1)">‹</button>
    <span class="px-2">Hal. {{ page }} / {{ lastPage }}</span>
    <button class="btn-secondary !px-2.5 !py-1" :disabled="page>=lastPage" @click="$emit('change', page+1)">›</button>
  </div>
</template>
<script setup>
defineProps({ page: { type: Number, default: 1 }, lastPage: { type: Number, default: 1 } });
defineEmits(['change']);
</script>
