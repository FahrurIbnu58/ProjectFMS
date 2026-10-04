import { reactive, computed } from 'vue';
import axios from 'axios';

export const auth = reactive({
    token: localStorage.getItem('fms_token') || '',
    user: JSON.parse(localStorage.getItem('fms_user') || 'null'),
});

export const isAuthenticated = computed(() => !!auth.token);

export const isAdmin = computed(() => {
    if (!auth.user) return false;
    const r = String(auth.user.role || '').toLowerCase();
    return r.includes('admin') || r.includes('administrator');
});

export async function login(email, password) {
    // Hapus sisa-sisa token/header lama sebelum mencoba login baru
    delete axios.defaults.headers.common['Authorization'];
    localStorage.removeItem('fms_token');
    localStorage.removeItem('fms_user');

    const response = await axios.post('/login', { email, password });
    const { token, user } = response.data;

    auth.token = token;
    auth.user = user;

    localStorage.setItem('fms_token', token);
    localStorage.setItem('fms_user', JSON.stringify(user));

    // Set Authorization Header untuk request berikutnya
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    return response;
}

export async function logout() {
    try {
        await axios.post('/logout');
    } catch (e) {
        // Abaikan error saat logout
    } finally {
        auth.token = '';
        auth.user = null;
        localStorage.removeItem('fms_token');
        localStorage.removeItem('fms_user');
        delete axios.defaults.headers.common['Authorization'];
    }
}