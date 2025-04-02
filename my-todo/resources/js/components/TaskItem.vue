<template>
    <li class="flex items-center space-x-2 border-b py-2">
        <span class="cursor-pointer" @click="$emit('toggle-task-status', task)">{{ task.task }}</span>
        <button @click="$emit('delete-task', task.id)" class="text-red-500">✖️</button>
    </li>
</template>

<script>
export default {
    props: { task: Object}
};
</script>