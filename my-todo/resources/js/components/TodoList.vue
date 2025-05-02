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
import { deleteTodo as apiDeleteTodo, updateOrder as apiUpdateOrder, updateTodo as apiUpdateTodo } from "../api/todo";
import { createTask, deleteTask as apiDeleteTask, toggleTaskStatus as apiToggleTaskStatus } from "../api/task";
import { onMounted } from "vue";


// props
const props = defineProps({
  initialTodos: Array
});

// reactive state
const todos = ref([...props.initialTodos]);

// methods
const deleteTodo = async (id) => {
  if (!confirm('本当に削除しますか？')) return;
  try {
    await apiDeleteTodo(id);
    todos.value = todos.value.filter(todo => todo.id !== id);
  } catch (error) {
    console.error('エラー：', error);
  }
};

const updateOrder = async () => {
  try {
    todos.value.forEach((todo, index) => {
      todo.order = index + 1;
    });
    
    await apiUpdateOrder(todos.value);
  } catch (error) {
    console.error('順番の保存に失敗しました', error);
  }
};

const updateTodo = async (updatedTodo) => {
  const index = todos.value.findIndex(todo => todo.id === updatedTodo.id);

  if (index !== -1) {
    todos.value[index].goal = updatedTodo.goal;
  } else {
    console.warn('Todoの更新に失敗しました');
  }

  try {
    await apiUpdateTodo({
      todo_id: updatedTodo.id,
      todo_goal: updatedTodo.goal
    });
  } catch (error) {
    console.error('更新に失敗しました', error);
  }
};

// task関係の関数(provideの使用)
const addTask = async (taskData) => {
  try {
    const createdTask = await createTask(taskData.id, taskData.task);

    // 対象のtodoを検索して追加
    const targetTodo = todos.value.find(todo => todo.id === taskData.id);

    if (targetTodo) {
      targetTodo.tasks.push(createdTask);
    }
  } catch (error) {
    console.error('通信エラー：', error);
  }
};
provide('addTask', addTask);

const deleteTask = async (taskData) => {
  try {
    await apiDeleteTask(taskData.id);
    const targetTodo = todos.value.find(todo => todo.id === taskData.goal_id);
    if (targetTodo) {
      targetTodo.tasks = targetTodo.tasks.filter(t => t.id !== taskData.id);
    } else {
      console.warn('該当するTodoが見つかりませんでした');
    }
  } catch (error) {
    console.error('通信エラー：', error);
  }
};
provide('deleteTask', deleteTask);

const toggleTaskStatus = async (nextStatus, id) => {
    try {
        await apiToggleTaskStatus(nextStatus, id);
    } catch (error) {
        console.error('通信エラー：', error);
        alert('ステータスの更新に失敗しました');
    }
};
provide('toggleTaskStatus', toggleTaskStatus);

const onToggleDetails = (todoId) => {
  const target = todos.value.find(t => t.id === todoId);
  if (target) {
    target.showDetails = !target.showDetails;
  }
};

</script>

<style scoped>
.cursor-pointer:hover {
  opacity: 0.8;
}
</style>
