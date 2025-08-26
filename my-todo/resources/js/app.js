import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import GoalList from './components/GoalList.vue';
import Alpine from 'alpinejs';
import { initCsrf } from './api/csrf';

initCsrf();

const el = document.getElementById('app');

if (el) {
    const app = createApp({});

    const pinia = createPinia();
    app.use(pinia);

    app.component('goal-list', GoalList);
    app.mount('#app');
}

// const app = createApp({});

// const pinia = createPinia();
// app.use(pinia);

// app.component('goal-list', GoalList);
// app.mount('#app');

window.Alpine = Alpine;

Alpine.start();
