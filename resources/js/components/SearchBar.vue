<template>
  <div class="flex flex-col sm:flex-row gap-2">
    <input v-model="q" @input="emit" type="search" placeholder="Cari berkas / folder…" class="input flex-1" />
    <select v-model="dept" @change="emit" class="input sm:w-52">
      <option value="">Semua Departemen</option>
      <option v-for="d in departments" :key="d?.id" :value="d?.id">{{ d?.name ?? d?.nama ?? '-' }}</option>
    </select>
  </div>
</template>
<script setup>
import { ref, watch } from 'vue';
const props = defineProps({ departments: { type: Array, default: () => [] }, modelValue: { type: Object, default: () => ({}) } });
const emitEv = defineEmits(['update:modelValue', 'search']);
const q = ref(props.modelValue?.q ?? '');
const dept = ref(props.modelValue?.department_id ?? '');
watch(() => props.modelValue, (v) => { q.value = v?.q ?? ''; dept.value = v?.department_id ?? ''; });
let t = null;
function emit() {
  clearTimeout(t);
  t = setTimeout(() => {
    const v = { q: q.value, department_id: dept.value };
    emitEv('update:modelValue', v);
    emitEv('search', v);
  }, 300);
}
</script>
