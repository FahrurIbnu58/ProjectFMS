import { createRouter, createWebHistory } from 'vue-router';
import { auth, isAdmin } from '../store/auth.js';
import UsersView from '../views/UsersView.vue';
import RegisterView from '../views/RegisterView.vue';
import ForgotPasswordView from '../views/ForgotPasswordView.vue';

const LoginView = () => import('../views/LoginView.vue');
const DashboardView = () => import('../views/DashboardView.vue');
const FolderBrowserView = () => import('../views/FolderBrowserView.vue');
const FileDetailView = () => import('../views/FileDetailView.vue');
const DepartmentsView = () => import('../views/DepartmentsView.vue');
const ActivityLogView = () => import('../views/ActivityLogView.vue');

const routes = [
    { path: '/login', name: 'login', component: LoginView, meta: { guest: true } },
    { path: '/register', name: 'register', component: RegisterView, meta: { guest: true } },
    { path: '/forgot-password', name: 'forgot-password', component: ForgotPasswordView, meta: { guest: true } },

    { path: '/', name: 'dashboard', component: DashboardView, meta: { auth: true } },
    { path: '/users', name: 'users', component: UsersView, meta: { auth: true, admin: true } },
    { path: '/folders/:id?', name: 'folders', component: FolderBrowserView, meta: { auth: true } },
    { path: '/files/:id', name: 'file-detail', component: FileDetailView, meta: { auth: true } },
    { path: '/departments', name: 'departments', component: DepartmentsView, meta: { auth: true, admin: true } },
    { path: '/logs', name: 'logs', component: ActivityLogView, meta: { auth: true, admin: true } },
    { path: '/:pathMatch(.*)*', redirect: '/' },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to) => {
    const token = auth.token || localStorage.getItem('fms_token');
    if (to.meta.auth && !token) return { path: '/login', query: { redirect: to.fullPath } };
    if (to.meta.guest && token) return { path: '/' };
    if (to.meta.admin && !isAdmin.value) {
        if (auth.user) return { path: '/' };
    }
    return true;
});

export default router;