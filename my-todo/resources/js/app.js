import './bootstrap';
import { createApp } from 'vue';
import TodoList from './components/TodoList.vue';
import Alpine from 'alpinejs';

const app = createApp({});
app.component('todo-list', TodoList);
app.mount('#app');

window.Alpine = Alpine;

Alpine.start();
