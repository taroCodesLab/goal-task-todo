<template>
    <div v-show="todo.showDetails" class="ml-8 mt-2 border-l-2 border-blue-400 pl-4">
        <p class="text-gray-600">タスク一覧</p>
        <form @submit.prevent="handleAddTask">
            <input v-model="newTask" type="text" class="border rounded w-64 py-1 px-2" placeholder="新しいタスクを追加" />
            <button type="submit" class="ml-2 px-4 py-1 bg-blue-500 text-white rounded">追加</button>
        </form>
        <ul>
            <TaskItem v-for="task in tasks" :key="task.id" :task="task" @update-task="$emit('update-task', {task: $event, todoId: todo.id })" @delete-task="deleteTask" />
        </ul>
    </div>
</template>

<script>
import { deleteTask } from "../api/task";
import TaskItem from "./TaskItem.vue";
export default {
    components: { TaskItem },
    props: { 
        tasks: Array,
        todo: {
            type: Object,
            required: true
        }
     },
    data() { return { newTask: ''}; },
    methods: {
        handleAddTask() {
            if (!this.newTask.trim()) return;
            const taskData = {
                id: this.todo.id,
                task: this.newTask,
                status: "未着手"
            };
            this.$emit('add-task', taskData);
            this.newTask = '';
        },
        async deleteTask(wantDeleteTask) {
            try {
                await deleteTask(wantDeleteTask.id);
                const todo = this.todo;
                todo.tasks = todo.tasks.filter(task => task.id !== wantDeleteTask.id);
            } catch (error) {
                console.error('エラー:', error);
            }
        }
    }
};
</script>