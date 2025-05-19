import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import TodoList from './components/TodoList.vue';
import Alpine from 'alpinejs';

const app = createApp({});

const pinia = createPinia();
app.use(pinia);

app.component('todo-list', TodoList);
app.mount('#app');

window.Alpine = Alpine;

Alpine.start();
