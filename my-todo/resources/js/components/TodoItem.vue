<template>
    <li class="w-full border-t border-gray-300 py-3 mx-auto">
        <div class="flex items-center space-x-2">
            <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500">
            <svg @click="toggleDetails" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 cursor-pointer translate-y-0.5" :class="{'text-gray-500': !todo.showDetails, 'text-blue-500': todo.showDetails}" viewBox="0 0 20 20" fill="currentColor">
                  <path :d="todo.showDetails ? closeIcon : openIcon" />
            </svg>
            <span @click="toggleDetails" @dblclick="editTodo" v-if="!isEditing" class="cursor-pointer w-full">{{ todo.goal }}</span>
            <input v-else v-model="editedGoal.goal" @blur="finishEdit" @keydown.enter.exact="onEnter" @compositionstart="isComposing = true" @compositionend="onCompositionEnd" ref="editInput" class="border rounded w-full px-2 py-1">
            <span v-if="todo.tasks.length > 0" class="text-sm">{{ calculateCompletionRate(todo) }}%</span>
            <button @click="deleteTodo" class="text-red-500 hover:text-red-700 px-2">✖️</button>
        </div>

        <!-- タスクリスト　-->
        <TaskList :tasks="todo.tasks" :todo-id="todo.id" :todo="todo" @add-task="addTask" />
    </li>
</template>

<script>
import TaskList from "./TaskList.vue";
import { createTask } from "../api/task";
export default {
    components: { TaskList },
    props: { todo: Object },
    data() {
        return {
            isComposing: false,
            isEditing: false,
            editedGoal: null,
            openIcon: "M10 3a1 1 0 01.707.293l4 4a1 1 0 01-1.414 1.414L10 5.414 6.707 8.707A1 1 0 015.293 7.293l4-4A1 1 0 0110 3z",
            closeIcon: "M10 17a1 1 0 01-.707-.293l-4-4a1 1 0 011.414-1.414L10 14.586l3.293-3.293a1 1 0 011.414 1.414l-4 4A1 1 0 0110 17z",
        };
    },
    methods: {
        toggleDetails() {
            this.todo.showDetails = !this.todo.showDetails;
        },
        deleteTodo() {
            const todoId = this.todo.id;
            this.$emit('delete-todo', todoId);
        },
        editTodo() {
            this.isEditing = true;
            this.editedGoal = { ...this.todo };
            this.$nextTick(() => this.$refs.editInput.focus());
        },
        onEnter() {
            if (!this.isComposing) {
                this.finishEdit();
            }
        },
        onCompositionEnd() {
            this.isComposing = false;
        },
        finishEdit() {
            this.$emit('update-todo', this.editedGoal);
            this.isEditing = false;

            // 次のDOM更新サイクルまで遅延
            this.$nextTick(() => {
                this.editedGoal = null;
            });
        },
        async addTask(taskData) {
            try {
                // api/task.jsのcreateTask
                const createdTask = await createTask(taskData.id, taskData.task);

                this.todo.tasks.push(createdTask);
                this.$emit('update-todo', this.todo);  
            } catch (error) {
                console.error('通信エラー:', error);
            }
        },
        calculateCompletionRate(todo) {
            const totalTasks = todo.tasks.length;
            const completedTasks = todo.tasks.filter(task => task.status === '完了').length;

            return totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0;
        },

    }
};
</script>