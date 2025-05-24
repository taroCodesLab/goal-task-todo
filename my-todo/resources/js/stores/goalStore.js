// stores/goalStore.js
import { defineStore } from 'pinia';
import { deleteJson, postJson, putJson } from "../utils/http";
import { deleteGoal as apiDeleteGoal, updateOrder as apiUpdateOrder, updateGoal as apiUpdateGoal } from "../api/goal";
import { createTask as apiCreateTask, deleteTask as apiDeleteTask, toggleTaskStatus as apiToggleTaskStatus } from "../api/task";

export const usegoalStore = defineStore('goal', {
    state: () => ({
        goals: []
    }),
    actions: {
        async addGoal(goal) {
            // 必要に応じてAPI通信を追加
            // 例: const newgoal = await postJson('/goal', goal);
            this.goals.push(goal);
        },
        async deleteGoal(id) {
            await apiDeleteGoal(id);
            this.goals = this.goals.filter(goal => goal.id !== id);
        },
        async updateGoal(updatedGoal) {
            await apiUpdateGoal({ goal_id: updatedGoal.id, goal_goal: updatedGoal.goal });
            const index = this.goals.findIndex(goal => goal.id === updatedGoal.id);
            if (index !== -1) {
                this.goals[index] = updatedGoal;
            }
        },
        toggleDetails(id) {
            const goal = this.goals.find(goal => goal.id === id);
            if (goal) {
                goal.showDetails = !goal.showDetails;
            }
        },
        async addTask(goalId, task) {
            const createdTask = await apiCreateTask(goalId, task);
            const goal = this.goals.find(goal => goal.id === goalId);
            if (goal) {
                goal.tasks.push(createdTask);
            }
        },
        async deleteTask(goalId, taskId) {
            await apiDeleteTask(taskId);
            const goal = this.goals.find(goal => goal.id === goalId);
            if (goal) {
                goal.tasks = goal.tasks.filter(task => task.id !== taskId);
            }
        },
        async updateTask(goalId, updatedTask) {
            const goal = this.goals.find(goal => goal.id === goalId);
            if (goal) {
                const index = goal.tasks.findIndex(task => task.id === updatedTask.id);
                if (index !== -1) {
                    goal.tasks[index] = updatedTask;
                }
            }
        },
        async updateOrder() {
            this.goals.forEach((goal, index) => {
                goal.order = index + 1;
            });
            await apiUpdateOrder(this.goals);
        },
        async updateTaskStatus(goalId, taskId, nextStatus) {
            await apiToggleTaskStatus(nextStatus, taskId);
            const goal = this.goals.find(goal => goal.id === goalId);
            if (goal) {
                const task = goal.tasks.find(task => task.id === taskId);
                if (task) {
                    task.status = nextStatus;
                }
            }
        }
    }
});