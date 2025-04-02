<template>
    <div class="ml-8 mt-2">
        <ul>
            <TaskItem v-for="task in tasks" :key="task.id" :task="task" />
            <form @submit.prevent="addTask">
                <input v-model="newTask" type="text" class="border.rounded" />
                <button type="submit" class="bg-blue-500 text-white rounded">追加</button>
            </form>
        </ul>
    </div>
</template>

<script>
import TaskItem from "./TaskItem.vue";
export default {
    props: { tasks: Array, todoId: Number },
    components: { TaskItem },
    data() { return { newTask: ''}; },
    methods: {
        addTask() {
            this.$emit('add-task', { todoId: this.todoId, task: this.newTask });
            this.newTask = '';
        }
    }
};
</script>