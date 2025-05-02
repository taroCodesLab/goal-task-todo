<template>
    <li class="flex items-center space-x-2 p-3 border-b w-11/12">
        <div class="flex items-center space-x-4 w-full">
            <div @click="handleToggleTaskStatus" class="w-4 h-4 rounded-full border flex items-center justify-center cursor-pointer" :class="statusClass"></div>
            <span class="ml-4 flex-grow">{{ task.task }}</span>
            <span @click="handleToggleTaskStatus" class="cursor-pointer" :class="statusClass">{{ task.status }}</span>
            <button @click="handleDeleteTask" class="text-red-500 hover:text-red-700 px-2">✖️</button>
        </div>
    </li>
</template>

<script setup>

import { computed, inject } from 'vue';

const props = defineProps({
    task: Object
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
        await toggleTaskStatus(nextStatus, props.task.id);
    } catch (error) {
        console.error('エラー：', error);
        alert('ステータスの更新に失敗しました');
    }
};

// タスク削除処理
const handleDeleteTask = async () => {
    const taskData = {
        id: props.task.id,
        task: props.task.task,
        status: props.task.status,
        goal_id: props.task.goal_id
    };

    try {
        await deleteTask(taskData);
    } catch (error) {
        console.error('削除エラー：', error);
    }
};
</script>