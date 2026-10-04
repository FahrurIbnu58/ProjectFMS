import axios from 'axios';

axios.defaults.baseURL = '/api';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const token = localStorage.getItem('fms_token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Global Interceptor untuk menangani token kedaluwarsa
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 401) {
            // Hanya redirect ke login jika error 401 bukan berasal dari endpoint /login itu sendiri
            if (!error.config.url.includes('/login')) {
                localStorage.removeItem('fms_token');
                localStorage.removeItem('fms_user');
                window.location.href = '/login';
            }
        }
        return Promise.reject(error);
    }
);