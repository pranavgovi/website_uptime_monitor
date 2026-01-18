import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';
import App from './App.vue';
import Home from './pages/Home.vue';

axios.defaults.baseURL = '/api/v1';

const routes = [
    {
        path: '/',
        name: 'home',
        component: Home
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

const app = createApp(App);
app.use(router);
app.mount('#app');
