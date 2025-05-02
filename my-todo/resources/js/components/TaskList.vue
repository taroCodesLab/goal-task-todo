<template>
    <div class="ml-8 mt-2 border-l-2 border-blue-400 pl-4">
        <p class="text-gray-600">タスク一覧</p>
        <form @submit.prevent="handleAddTask">
            <input v-model="newTask" type="text" class="border rounded w-64 py-1 px-2" placeholder="新しいタスクを追加" />
            <button type="submit" class="ml-2 px-4 py-1 bg-blue-500 text-white rounded">追加</button>
        </form>
        <ul>
            <TaskItem v v-for="task in tasks" :key="task.id" :task="task" />
        </ul>
    </div>
</template>

<script setup>

import { ref, inject } from 'vue';
import TaskItem from './TaskItem.vue';

// Props
const props = defineProps({
    tasks: Array,
    todo: {
        type: Object,
        required: true
    }
});


// 状態
const newTask = ref('');

// inject　で親から関数を取得
const addTask = inject('addTask');

// タスク追加処理
const handleAddTask = async () => {
    if (!newTask.value.trim()) return;

    const taskData = {
        id: props.todo.id,
        task: newTask.value,
        status: '未着手'
    };

    try {
        await addTask(taskData);
        newTask.value = '';
    } catch (error) {
        console.error('タスク追加エラー：', error);
    }
};

</script>