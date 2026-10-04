<template>
    <div class="min-h-screen flex items-center justify-center bg-slate-900 p-4">
        <div
            class="bg-slate-800 border border-slate-700/60 rounded-2xl w-full max-w-md p-6 sm:p-8 shadow-2xl space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Atur Ulang Password</h2>
                <p class="text-slate-400 text-sm mt-1">Masukkan email dan password baru Anda</p>
            </div>

            <div v-if="message"
                class="p-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs rounded-xl">
                {{ message }}
            </div>

            <div v-if="errorMessage"
                class="p-3 bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs rounded-xl">
                {{ errorMessage }}
            </div>

            <form @submit.prevent="handleReset" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email Terdaftar</label>
                    <input v-model="email" type="email" required placeholder="nama@domain.com"
                        class="w-full px-3.5 py-2.5 bg-slate-900/60 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Password Baru</label>
                    <input v-model="newPassword" type="password" required placeholder="Minimal 6 karakter"
                        class="w-full px-3.5 py-2.5 bg-slate-900/60 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <button type="submit" :disabled="loading"
                    class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl text-sm transition shadow-lg shadow-blue-600/20 disabled:opacity-50">
                    {{ loading ? 'Memproses...' : 'Simpan Password Baru' }}
                </button>
            </form>

            <div class="pt-4 border-t border-slate-700/60 text-center">
                <router-link to="/login" class="text-blue-400 font-semibold text-xs hover:underline">
                    ← Kembali ke Halaman Login
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const email = ref('');
const newPassword = ref('');
const message = ref('');
const errorMessage = ref('');
const loading = ref(false);
const router = useRouter();

async function handleReset() {
    loading.value = true;
    message.value = '';
    errorMessage.value = '';
    try {
        const { data } = await axios.post('/forgot-password', {
            email: email.value,
            new_password: newPassword.value
        });
        message.value = data.message;
        setTimeout(() => {
            router.push('/login');
        }, 1500);
    } catch (err) {
        errorMessage.value = err.response?.data?.message || 'Gagal mengatur ulang password.';
    } finally {
        loading.value = false;
    }
}
</script>