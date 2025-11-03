<template>
    <li class="flex items-center space-x-2 p-3 border-b w-11/12">
        <div class="flex items-center space-x-4 w-full">
            <div @click="handleToggleTaskStatus" class="w-4 h-4 rounded-full border flex items-center justify-center cursor-pointer" :class="statusClass"></div>
            <span class="ml-4 flex-grow">{{ task.task }}</span>
            <span @click="handleToggleTaskStatus" class="cursor-pointer" :class="statusClass">{{ task.status }}</span>
            <button @click="handleDeleteTask" class="bg-red-500 hover:bg-red-700 text-white rounded-full w-5 flex items-center justify-center shadow-md"><span class="text-xl leading-none font-bold">-</span></button>
        </div>
    </li>
</template>

<script setup>

import { computed, inject } from 'vue';

const props = defineProps({
    task: Object,
    goalId: Number,
});

const emit = defineEmits(['delete-task']);

//inject
const deleteTask = inject('deleteTask');
const toggleTaskStatus = inject('toggleTaskStatus');

// ステータスに応じたクラス
const statusClass = computed(() => {
    return {
        "bg-gray-300 px-2 py-1 rounded": props.task.status === "未着手",
        "bg-yellow-300 px-2 py-1 rounded": props.task.status === "進行中",
        "bg-green-500 px-2 py-1 rounded text-white": props.task.status === "完了"
    };
});

// 次のステータスを取得
const getNextStatus = (currentStatus) => {
    const statusCycle = ['未着手', '進行中', '完了'];
    const nextIndex = (statusCycle.indexOf(currentStatus) + 1) % statusCycle.length;

    return statusCycle[nextIndex];
};

// ステータスの切り替え処理
const handleToggleTaskStatus = async () => {
    const nextStatus = getNextStatus(props.task.status);
    props.task.status = nextStatus;

    try {
        await toggleTaskStatus(nextStatus, props.task.id, props.goalId);
    } catch (error) {
        console.error('Operation failed:', e);
        notifyError(e);
    }
};

// タスク削除処理
const handleDeleteTask = async () => {
    const taskData = {
        id: props.task.id,
        task: props.task.task,
        status: props.task.status,
        goal_id: props.goalId
    };

    try {
        await deleteTask(taskData);
    } catch (error) {
        console.error('Operation failed:', e);
        notifyError(e);
    }
};
</script>