import { reactive, computed } from 'vue';
import axios from 'axios';

export const auth = reactive({
    user: null,
    token: localStorage.getItem('fms_token') || null,
});

export const isAdmin = computed(() => {
    const r = auth.user?.role ?? auth.user?.role_name ?? '';
    return r === 'administrator' || r === 'admin';
});

export const roleName = computed(() => auth.user?.role ?? auth.user?.role_name ?? '-');

export async function login(email, password) {
    const { data } = await axios.post('/login', { email, password });
    // Defensive: backend may return {token,user} or {data:{token,user}} or {access_token}
    const token = data?.token ?? data?.data?.token ?? data?.access_token ?? data?.data?.access_token ?? null;
    const user = data?.user ?? data?.data?.user ?? null;
    if (!token) throw new Error(data?.message || 'Token tidak diterima dari server.');
    auth.token = token;
    auth.user = user;
    localStorage.setItem('fms_token', token);
    if (user) localStorage.setItem('fms_user', JSON.stringify(user));
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    // If user missing, try /me
    if (!user) {
        try { await fetchMe(); } catch { /* ignore */ }
    }
    return { token, user: auth.user };
}

export async function fetchMe() {
    const { data } = await axios.get('/me');
    const user = data?.user ?? data?.data ?? data;
    auth.user = user;
    localStorage.setItem('fms_user', JSON.stringify(user));
    return user;
}

export async function logout() {
    try { await axios.post('/logout'); } catch { /* ignore */ }
    auth.user = null;
    auth.token = null;
    localStorage.removeItem('fms_token');
    localStorage.removeItem('fms_user');
    delete axios.defaults.headers.common['Authorization'];
}
