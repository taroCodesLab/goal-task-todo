<template>
    <li class="flex items-center space-x-2 p-3 border-b w-11/12">
        <div class="flex items-center space-x-4 w-full">
            <div @click="toggleTaskStatus" class="w-4 h-4 rounded-full border flex items-center justify-center cursor-pointer" :class="statusClass"></div>
            <span class="ml-4 flex-grow">{{ task.task }}</span>
            <span @click="toggleTaskStatus" class="cursor-pointer" :class="statusClass">{{ task.status }}</span>
            <button @click="deleteTask" class="text-red-500 hover:text-red-700 px-2">✖️</button>
        </div>
    </li>
</template>

<script>
import { toggleTaskStatus } from "../api/task";
export default {
    props: { task: Object},
    computed: {
        statusClass() {
            return {
                "bg-gray-300 px-2 py-1 rounded": this.task.status === "未着手",
                "bg-yellow-300 px-2 py-1 rounded": this.task.status === "進行中",
                "bg-green-500 px-2 py-1 rounded text-white": this.task.status === "完了"
            };
        }
    },
    methods: {
        getNextStatus(currentStatus) {
            const statusCycle = ["未着手", "進行中", "完了"];
            const nextIndex = (statusCycle.indexOf(currentStatus) + 1) % statusCycle.length;

            return statusCycle[nextIndex];
        },
        async toggleTaskStatus() {
            // 状態をループ切り替え
            const nextStatus = this.getNextStatus(this.task.status);
            this.task.status = nextStatus;

            // APIでDBの状態を更新
            try {
                await toggleTaskStatus(nextStatus, this.task.id);
            } catch (error) {
                console.error('エラー:', error);
                alert('ステータスの更新に失敗しました');
            }
        },
        deleteTask() {
            const wantDeleteTask = {
                id: this.task.id,
                task: this.task.task,
                status: this.task.status,
                goal_id: this.task.goal_id
            };
            this.$emit('delete-task', wantDeleteTask);
        }
    }
};
</script>