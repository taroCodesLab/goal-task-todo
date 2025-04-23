<template>
  <div class="w-full flex justify-center items-center">
    <div class="w-full max-w-xl">
        <draggable v-model="todos" @end="updateOrder" item-key="id" tag="ul" class="mt-6 space-y-3 flex flex-col jutify-center">
          <template #item="{ element: todo }">
            <TodoItem :key="todo.id" :todo="todo" @delete-todo="deleteTodo" @update-todo="updateTodo" />
          </template>
        </draggable>
    </div>
  </div>
</template>

<script>
import draggable from "vuedraggable";
import TodoItem from "./TodoItem.vue";
import { deleteTodo, updateOrder, updateTodo } from "../api/todo";

export default {
  components: {draggable, TodoItem},
  props: {initialTodos: Array},
  data() {
    return { todos: [...this.initialTodos] };
  },
  methods: {
    async deleteTodo(id) {
      if (!confirm('本当に削除しますか？')) return;
      try {
        // api/todo.jsのdeleteTodo
        await deleteTodo(id);

        this.todos = this.todos.filter(todo => todo.id !== id);
      } catch (error) {
        console.error('エラー:', error);
      }
    },
    // ToDoの並び順の変更を加える
    async updateOrder() {
      try {
        this.todos.forEach((todo, index) => {
          todo.order = index + 1;
        });
        // api/todo.jsのupdateOrder
        await updateOrder(this.todos);
        
      } catch (error) {
        console.error('エラー:', error);
        alert('順番の保存に失敗しました');
      }
    },
    async updateTodo(updatedTodo) {
      const todoId = updatedTodo.id;

      // 楽観的uiを実施
      const index = this.todos.findIndex(todo => todo.id === todoId);
      if (index !== -1) {
        // 先に画面を更新
        this.todos[index].goal = updatedTodo.goal;
      } else {
        console.warn('Todoの更新に失敗しました');
      }
      try {

        // api/todo.jsのupdateTodo
        await updateTodo({
          todo_id: todoId, 
          todo_goal: updatedTodo.goal
        });
        

      } catch (error) {
        console.error('エラー:', error);
        alert('更新に失敗しました');
      }
    }
  }
};
</script>

<style scoped>
.cursor-pointer:hover {
  opacity: 0.8;
}
</style>
