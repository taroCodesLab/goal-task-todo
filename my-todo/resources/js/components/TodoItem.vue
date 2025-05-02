<template>
    <li class="w-full border-t border-gray-300 py-3 mx-auto">
        <div class="flex items-center space-x-2">
            <input type="checkbox" class="h-5 w-5 text-green-500">
            <svg @click="toggleDetails" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 cursor-pointer translate-y-0.5" :class="{'text-gray-500': !todo.showDetails, 'text-blue-500': todo.showDetails}" viewBox="0 0 20 20" fill="currentColor">
                  <path :d="todo.showDetails ? closeIcon : openIcon" />
            </svg>
            <span @click="toggleDetails" @dblclick="editTodo" v-if="!isEditing" class="cursor-pointer w-full">{{ todo.goal }}</span>
            <input v-else v-model="editedGoal.goal" @blur="finishEdit" @keydown.enter.exact="onEnter" @compositionstart="isComposing = true" @compositionend="onCompositionEnd" ref="editInput" class="border rounded w-full px-2 py-1">
            <span v-if="todo.tasks.length > 0" class="text-sm">{{ completionRate }}%</span>
            <button @click="deleteTodo" class="text-red-500 hover:text-red-700 px-2">✖️</button>
        </div>

        <!-- タスクリスト　-->
        <TaskList v-show="todo.showDetails" :tasks="todo.tasks" :todo-id="todo.id" :todo="todo" />
    </li>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import TaskList from './TaskList.vue';

const props = defineProps({
    todo: Object
});

const emit = defineEmits(['delete-todo', 'update-todo', 'toggle-details']);

const isEditing = ref(false);
const isComposing = ref(false);
const editedGoal = ref(null);
const editInput = ref(null);

const openIcon = "M10 3a1 1 0 01.707.293l4 4a1 1 0 01-1.414 1.414L10 5.414 6.707 8.707A1 1 0 015.293 7.293l4-4A1 1 0 0110 3z";
const closeIcon = "M10 17a1 1 0 01-.707-.293l-4-4a1 1 0 011.414-1.414L10 14.586l3.293-3.293a1 1 0 011.414 1.414l-4 4A1 1 0 0110 17z";

const toggleDetails = () => {
    emit('toggle-details', props.todo.id);
};

const deleteTodo = () => {
    emit('delete-todo', props.todo.id);
}

const editTodo = () => {
    isEditing.value = true;
    editedGoal.value = { ...props.todo };
    nextTick(() => {
        editInput.value?.focus();
    });
};

const onEnter = () => {
    if (!isComposing.value) {
        finishEdit();
    }
}

const onCompositionEnd = () => {
    isComposing.value = false;
};

const finishEdit = () => {
    emit('update-todo', editedGoal.value);
    isEditing.value = false;
    nextTick(() => {
        editedGoal.value = null;
    });
};

const completionRate = computed(() => {
    const totalTasks = props.todo.tasks.length;
    const completedTasks = props.todo.tasks.filter(task => task.status === '完了').length;

    return totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0;
});

</script>
<style scoped>
.cursor-pointer:hover {
    opacity: 0.8
}
</style>