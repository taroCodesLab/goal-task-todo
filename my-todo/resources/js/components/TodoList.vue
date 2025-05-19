<template>
  <div class="w-full flex justify-center items-center">
    <div class="w-full max-w-xl">
        <draggable v-model="todos" @end="updateOrder" item-key="id" tag="ul" class="mt-6 space-y-3 flex flex-col jutify-center">
          <template #item="{ element: todo }">
            <TodoItem :todo="todo" @delete-todo="deleteTodo" @update-todo="updateTodo" @toggle-details="onToggleDetails"/>
          </template>
        </draggable>
    </div>
  </div>
</template>

<script setup>
import { ref, provide } from "vue";
import draggable from "vuedraggable";
import TodoItem from "./TodoItem.vue";
import { useTodoStore } from '../stores/todoStore';
import { storeToRefs } from 'pinia';
import { onMounted } from "vue";


// props
const props = defineProps({
  initialTodos: Array
});

const todoStore = useTodoStore();
const { todos } = storeToRefs(todoStore);

onMounted(() => {
  todoStore.todos = [...props.initialTodos];
});

// methods
const deleteTodo = async (id) => {
  if (!confirm('本当に削除しますか？')) return;
  try {
    await todoStore.deleteTodo(id);
  } catch (error) {
    console.error('エラー：', error);
  }
};

const updateOrder = async () => {
  try {
    await todoStore.updateOrder();
  } catch (error) {
    console.error('順番の保存に失敗しました', error);
  }
};

const updateTodo = async (updatedTodo) => {
  try {
    await todoStore.updateTodo(updatedTodo);
  } catch (error) {
    console.error('更新に失敗しました', error);
  }
};

// task関係の関数(provideの使用)
const addTask = async (taskData) => {
  try {
    await todoStore.addTask(taskData.id, taskData.task);
  } catch (error) {
    console.error('通信エラー：', error);
  }
};
provide('addTask', addTask);

const deleteTask = async (taskData) => {
  try {
    await todoStore.deleteTask(taskData.goal_id, taskData.id);
  } catch (error) {
    console.error('通信エラー：', error);
  }
};
provide('deleteTask', deleteTask);

const toggleTaskStatus = async (nextStatus, id, goalId) => {
  try {
    await todoStore.updateTaskStatus(goalId, id, nextStatus);
  } catch (error) {
    console.error('通信エラー：', error);
    alert('ステータスの更新に失敗しました');
  }
};
provide('toggleTaskStatus', toggleTaskStatus);

const onToggleDetails = (todoId) => {
  todoStore.toggleDetails(todoId);
};

</script>

<style scoped>
.cursor-pointer:hover {
  opacity: 0.8;
}
</style>
