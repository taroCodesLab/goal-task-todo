<template>
    <li class="border-t py-3">
        <div class="flex items-center space-x-2">
            <input type="text" class="form-checkbox h-5 w-5">
            <span @dblclick="editTodo" class="cursor-pointer w-full"{{ todo.goal }}></span>
            <button @click="$emit('delete-todo', todo.id)" class="text-red-500">✖️</button>
        </div>
        <TaskList :tasks="todo.tasks" :todo-id="todo.id" />
    </li>
</template>

<script>
import TaskList from "./TaskList.vue";
export default {
    props: { todo: Object },
    components: { TaskList },
    methods: {
        editTodo() { this.todo.isEditing = true; }
    }
};
</script>