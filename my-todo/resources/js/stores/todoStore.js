// stores/todoStore.js
import { defineStore } from 'pinia';
import { deleteJson, postJson, putJson } from "../utils/http";
import { deleteTodo as apiDeleteTodo, updateOrder as apiUpdateOrder, updateTodo as apiUpdateTodo } from "../api/todo";
import { createTask as apiCreateTask, deleteTask as apiDeleteTask, toggleTaskStatus as apiToggleTaskStatus } from "../api/task";

export const useTodoStore = defineStore('todo', {
    state: () => ({
        todos: []
    }),
    actions: {
        async addTodo(todo) {
            // 必要に応じてAPI通信を追加
            // 例: const newTodo = await postJson('/todo', todo);
            this.todos.push(todo);
        },
        async deleteTodo(id) {
            await apiDeleteTodo(id);
            this.todos = this.todos.filter(todo => todo.id !== id);
        },
        async updateTodo(updatedTodo) {
            await apiUpdateTodo({ todo_id: updatedTodo.id, todo_goal: updatedTodo.goal });
            const index = this.todos.findIndex(todo => todo.id === updatedTodo.id);
            if (index !== -1) {
                this.todos[index] = updatedTodo;
            }
        },
        toggleDetails(id) {
            const todo = this.todos.find(todo => todo.id === id);
            if (todo) {
                todo.showDetails = !todo.showDetails;
            }
        },
        async addTask(todoId, task) {
            const createdTask = await apiCreateTask(todoId, task);
            const todo = this.todos.find(todo => todo.id === todoId);
            if (todo) {
                todo.tasks.push(createdTask);
            }
        },
        async deleteTask(todoId, taskId) {
            await apiDeleteTask(taskId);
            const todo = this.todos.find(todo => todo.id === todoId);
            if (todo) {
                todo.tasks = todo.tasks.filter(task => task.id !== taskId);
            }
        },
        async updateTask(todoId, updatedTask) {
            const todo = this.todos.find(todo => todo.id === todoId);
            if (todo) {
                const index = todo.tasks.findIndex(task => task.id === updatedTask.id);
                if (index !== -1) {
                    todo.tasks[index] = updatedTask;
                }
            }
        },
        async updateOrder() {
            this.todos.forEach((todo, index) => {
                todo.order = index + 1;
            });
            await apiUpdateOrder(this.todos);
        },
        async updateTaskStatus(todoId, taskId, nextStatus) {
            await apiToggleTaskStatus(nextStatus, taskId);
            const todo = this.todos.find(todo => todo.id === todoId);
            if (todo) {
                const task = todo.tasks.find(task => task.id === taskId);
                if (task) {
                    task.status = nextStatus;
                }
            }
        }
    }
});