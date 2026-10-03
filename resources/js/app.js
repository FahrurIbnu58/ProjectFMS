import '../css/app.css';
import { createApp } from 'vue';
import axios from 'axios';
import router from './router/index.js';
import { auth } from './store/auth.js';
import AppLayout from './components/AppLayout.vue';

// Axios global setup
axios.defaults.baseURL = '/api';
axios.defaults.headers.common['Accept'] = 'application/json';
const savedToken = localStorage.getItem('fms_token');
if (savedToken) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${savedToken}`;
}
axios.interceptors.response.use(
    (res) => res,
    (err) => {
        if (err?.response?.status === 401) {
            auth.token = null;
            auth.user = null;
            localStorage.removeItem('fms_token');
            localStorage.removeItem('fms_user');
            delete axios.defaults.headers.common['Authorization'];
            if (router.currentRoute.value.path !== '/login') router.push('/login');
        }
        return Promise.reject(err);
    }
);

// Dark mode init
const darkPref = localStorage.getItem('fms_dark');
if (darkPref === '1' || (!darkPref && window.matchMedia?.('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
}

// Restore auth user from storage
try {
    const u = localStorage.getItem('fms_user');
    if (u && savedToken) auth.user = JSON.parse(u);
    if (savedToken) auth.token = savedToken;
} catch { /* ignore */ }

const app = createApp(AppLayout);
app.use(router);
app.mount('#app');
