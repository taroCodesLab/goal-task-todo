
import { defineStore } from 'pinia';
// import { deleteJson, postJson, putJson } from "../utils/http";
import { createGoal as apiCreateGoal, deleteGoal as apiDeleteGoal, updateOrder as apiUpdateOrder, updateGoal as apiUpdateGoal } from "../api/goal";
import { createTask as apiCreateTask, deleteTask as apiDeleteTask, toggleTaskStatus as apiToggleTaskStatus } from "../api/task";
import { importGoalsFromGuest } from '../api/import';

const LOCAL_STORAGE_KEY = 'guest_goals';


export const useGoalStore = defineStore('goal', {
    state: () => ({
        goals: [],
        mode: 'guest'
    }),

    getters: {
        totalTasksCount: (state) => {
            return state.goals.reduce((sum, goal) => sum + goal.tasks.length, 0);
        },
    },

    actions: {
        setMode(mode) {
            this.mode = mode;
        },
        async addGoal(goal) {
            if (this.isGoalLimitedReached) {
                return;
            }

            if (this.mode === 'user') {
                const createdGoal = await apiCreateGoal(goal);
                this.goals.unshift(createdGoal);
                await this.updateOrder();
            } else {
                const newGoal = {
                    id: Date.now(),
                    goal: goal,
                    createdAt: new Date().toISOString(),
                    tasks: []
                };
                this.goals.unshift(newGoal);
                this.saveToLocalStorage();
            }
        },
        async deleteGoal(id) {
            if (this.mode === 'user') {
                await apiDeleteGoal(id);
                this.goals = this.goals.filter(goal => goal.id !== id);
            } else {
                this.goals = this.goals.filter(goal => goal.id !== id);
                this.saveToLocalStorage();
            }
            
        },
        async updateGoal(updatedGoal) {
            console.log(updatedGoal);
            if (this.mode === 'user') {
                await apiUpdateGoal({ goal_id: updatedGoal.id, goal_goal: updatedGoal.goal });
                const index = this.goals.findIndex(goal => goal.id === updatedGoal.id);
                if (index !== -1) {
                    this.goals[index] = updatedGoal;
                }
            } else {
                const index = this.goals.findIndex(goal => goal.id === updatedGoal.id);
                if (index !== -1) {
                    this.goals[index] = updatedGoal;
                    this.saveToLocalStorage();
                }
            } 
        },
        toggleDetails(id) {
            const goal = this.goals.find(goal => goal.id === id);
            if (goal) {
                goal.showDetails = !goal.showDetails;
            }
        },
        async addTask(goalId, task) {
            if (this.mode === 'user') {
                const createdTask = await apiCreateTask(goalId, task);
                const goal = this.goals.find(goal => goal.id === goalId);
                if (goal) {
                    goal.tasks.push(createdTask);
                }
            } else {
                const goal = this.goals.find(goal => goal.id === goalId);
                if (goal) {
                    if (!Array.isArray(goal.tasks)) {
                        goal.tasks = [];
                    }

                    const newTask = {
                        id: Date.now(),
                        task: task,
                        createdAt: new Date().toISOString(),
                        goal_id: goalId,
                        status: '未着手'
                    };
                    goal.tasks.push(newTask);
                    this.saveToLocalStorage();
                }
            }
        },
        async deleteTask(goalId, taskId) {
            if (this.mode === 'user') {
                await apiDeleteTask(goalId, taskId);
                const goal = this.goals.find(goal => goal.id === goalId);
                if (goal) {
                    goal.tasks = goal.tasks.filter(task => task.id !== taskId);
                }
            } else {
                const goal = this.goals.find(goal => goal.id === goalId);
                if (goal) {
                    goal.tasks = goal.tasks.filter(task => task.id !== taskId);
                    this.saveToLocalStorage();
                }
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
            if (this.mode === 'user') {
                await apiUpdateOrder(this.goals);
            }
        },
        async updateTaskStatus(nextStatus, taskId, goalId) {
            if (this.mode === 'user') {
                await apiToggleTaskStatus(nextStatus, taskId, goalId);
                const goal = this.goals.find(goal => goal.id === goalId);
                if (goal) {
                    const task = goal.tasks.find(task => task.id === taskId);
                    if (task) {
                        task.status = nextStatus;
                    }
                }
            } else {
                const goal = this.goals.find(goal => goal.id === taskId);
                if (goal) {
                    const task = goal.tasks.find(task => task.id === taskId);
                    if (task) {
                        task.status = nextStatus;
                    }
                }
            }
        },
        // addLocalGoal(goal) {
        //     const newGoal = {
        //         id: Date.now(),
        //         goal: goal,
        //         createdAt: new Date().toISOString(),
        //     };
        //     this.goals.push(newGoal);
        //     console.log(goal);
        //     console.log(newGoal);
        //     this.saveToLocalStorage();
        // },
        saveToLocalStorage() {
            localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(this.goals));
        },
        loadFromLocalStorage() {
            const data = localStorage.getItem(LOCAL_STORAGE_KEY);
            if (data) {
                try {
                    const parsed = JSON.parse(data);
                    this.goals = Array.isArray(parsed) ? parsed.filter(goal => typeof goal === 'object' && goal.goal) : [];
                } catch (error) {
                    console.error('ローカルストレージからのデータのパーズに失敗しました', error);
                    this.goals = [];
                }
            } else {
                this.goals = [];
            }
        },
        clearLocalGoals() {
            this.goals = [];
            localStorage.removeItem(LOCAL_STORAGE_KEY);
        },
        async transferLocalGoalsToServer() {
            try {
                if (this.mode !== 'guest') return;

                const localGoals = JSON.parse(localStorage.getItem('guest_goals') || 'null');
                
                if (Array.isArray(localGoals) && localGoals.length > 0) {
                    const importedGoals = await importGoalsFromGuest(localGoals);
                    this.goals = [...importedGoals];
                    await this.updateOrder();
                    localStorage.removeItem('guest_goals');
                }

            } catch (error) {
                console.error('ゲストgoalのインポートに失敗:', error);
            }
        }
    }
});