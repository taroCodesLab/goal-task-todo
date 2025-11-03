import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import GoalList from './components/GoalList.vue';
import Alpine from 'alpinejs';
import { initCsrf } from './api/csrf';
import i18n from './plugins/i18n';
import Toast, { POSITION } from 'vue-toastification'
import 'vue-toastification/dist/index.css';

initCsrf();

const el = document.getElementById('app');

const options = {
    position: POSITION.TOP_RIGHT,
    timeout: 4000,
    closeOnClick: true,
    pauseOnHover: true,
    draggable: true,
    transition: 'Vue-Toastification__fade',
};

if (el) {
    const app = createApp({});

    const pinia = createPinia();
    app.use(pinia);
    app.use(i18n);

    app.use(Toast, options);

    app.component('goal-list', GoalList);
    app.mount('#app');
}

window.Alpine = Alpine;

Alpine.start();
