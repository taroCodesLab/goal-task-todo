<template>
    <div class="ml-8 mt-2 border-l-2 border-blue-400 pl-4">
        <p class="text-gray-600">Task</p>
        <form @submit.prevent="handleAddTask">
                <input v-model="taskField.fieldValue.value" type="text" name="task" id="task" class="border rounded w-2/3 sm:w-64 py-1 px-2 " placeholder="Task" :disabled="isLimitReached" />
                <p v-if="taskField.error.value" class="absolute top-0p left-0p text-red-500 text-xs italic mt-1"> {{ taskField.error.value }}</p>
            <button type="submit" class="ml-2 px-4 py-1 bg-blue-500 text-white rounded" :disabled="isLimitReached" >+</button>
        </form>
        <ul class="mt-4">
            <TaskItem v-for="task in tasks" :key="task.id" :task="task" :goal-id="goal.id"/>
        </ul>
    </div>
</template>

<script setup>

import { ref, inject } from 'vue';
import { useI18n } from 'vue-i18n';
import { storeToRefs } from 'pinia';
import TaskItem from './TaskItem.vue';
import { useGoalStore } from '../stores/goalStore';
import { useItemLimit } from '../composables/useItemLimit';
import { useFieldValidation, rules } from "../composables/useFieldValidation";


// Props
const props = defineProps({
    tasks: Array,
    goal: {
        type: Object,
        required: true
    }
});


const { t } = useI18n();

const goalStore = useGoalStore();
// inject　で親から関数を取得
const addTask = inject('addTask');

//Validation
const taskField = useFieldValidation('', [
    rules.required(t('validation.required')),
    rules.maxLength(255, t('validation.maxLength', { length: 255 }))
]);

const { isLimitReached, setLimitError } = useItemLimit({
    store: goalStore,
    maxCount: 5,
    countSelector: s => s.totalTasksCount,
    modeSelector: s => s.mode
});

setLimitError(taskField, t('validation.guestLimit'));

// タスク追加処理
const handleAddTask = async () => {
    if (isLimitReached.value) return;

    taskField.validate();
    
    if (!taskField.error.value) {
        const taskData = {
            id: props.goal.id,
            task: taskField.fieldValue.value,
            status: 'todo'
        };

        try {
            await addTask(taskData);
            taskField.setValueSilently('');

            if (!isLimitReached.value) {
                taskField.clearError();
            }
        } catch (error) {
            console.error('Operation failed:', e);
            notifyError(e || 'Failed to add task');
        }
    } else {
        console.log(taskField.error.value);
    }
};

</script>