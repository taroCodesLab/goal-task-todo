<template>
  <div class="w-full flex justify-center items-center">
    <div class="w-full max-w-xl">
        <draggable v-model="todos" @end="updateOrder" item-key="id" tag="ul" class="mt-6 space-y-3 flex flex-col jutify-center">
          <template #item="{ element: todo }">
            <li class="w-full border-t border-gray-300 py-3 mx-auto">
              <div class="flex items-center space-x-2">
                <input type="checkbox" class="form-checkbox h-5 w-5 text-green-500">
                <svg @click="toggleDetails(todo)" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 cursor-pointer translate-y-0.5" :class="{'text-gray-500': !todo.showDetails, 'text-blue-500': todo.showDetails}" viewBox="0 0 20 20" fill="currentColor">
                  <path :d="todo.showDetails ? closeIcon : openIcon" />
                </svg>
                <input v-if="todo.isEditing" type="text" v-model="todo.goal" @blue="finishEdit(todo)" @keydown.enter="finishEdit(todo)" class="border rounded w-full px-2 py-1" ref="editInput">
                <span @click="toggleDetails(todo)" v-else
                @dblclick="editTodo(todo)" class="text-gray-700 break-words w-full cursor-pointer">
                  {{ todo.goal }}
                </span>
                <span v-if="todo.tasks.length > 0" class="text-sm">{{ calculateCompletionRate(todo) }}%</span>
                <button @click="deleteTodo(todo.id)" class="text-red-500 hover:text-red-700 px-2">✖️</button>
              </div>
              <!-- 詳細部分 -->
              <div v-show="todo.showDetails" class="ml-8 mt-2 border-l-2 border-blue-400 pl-4">
                <p class="text-gray-600">タスク一覧</p>
                <!-- タスク追加フォーム -->
                <form @submit.prevent="addTask(todo)">
                  <input v-model="newTask" type="text" class="border rounded w-64 py-1 px-2" placeholder="新しいタスクを追加">
                  <button type="submit" class="ml-2 px-4 py-1 bg-blue-500 text-white rounded">追加</button>
                </form>
                <ul>
                  <li v-for="task in todo.tasks" :key="task.id" class="flex items-center space-x-2  p-3 border-b w-11/12">
                    <div class="flex items-center space-x-4 w-full">
                      <!-- ステータス切り替えボタン (左側) -->
                      <div
                        @click="toggleTaskStatus(task)"
                        class="w-4 h-4 rounded-full border flex items-center justify-center cursor-pointer"
                        :class="statusClass(task.status)">
                      </div>
                      <!-- タスク名 -->
                      <span class="ml-4 flex-grow">{{ task.task }}</span>
                      <!-- ステータス表示 (右端) -->
                      <span
                        @click="toggleTaskStatus(task)"
                        class="cursor-pointer"
                        :class="statusClass(task.status)">
                        {{ task.status }}
                      </span>
                      <button @click="deleteTask(todo.id, task.id)" class="text-red-500 hover:text-red-700 px-2">✖️</button>
                    </div>
                  </li>
                </ul>
              </div>
            </li>
          </template>]
        </draggable>
    </div>
  </div>
</template>

<script>
import draggable from "vuedraggable";

export default {
  components: {
    draggable,
  },
  props: {
    initialTodos: {
        type: Array,
        required: true,
    }
  },
  data() {
    return {
      todos: this.initialTodos.map((todo, index) => ({
        ...todo,
        isEditing: false,
        order: todo.order || index + 1,
        showDetails: false,
        tasks: todo.tasks || []
      }))
      .sort((a, b) => a.order - b.order),
      newTask: ''
    };
  },
  computed: {
    openIcon() {
      return 'M10 3a1 1 0 01.707.293l4 4a1 1 0 01-1.414 1.414L10 5.414 6.707 8.707A1 1 0 015.293 7.293l4-4A1 1 0 0110 3z';
    },
    closeIcon() {
      return 'M10 17a1 1 0 01-.707-.293l-4-4a1 1 0 011.414-1.414L10 14.586l3.293-3.293a1 1 0 011.414 1.414l-4 4A1 1 0 0110 17z';
    }
  },
  methods: {
    toggleDetails(todo) {
      todo.showDetails = !todo.showDetails;
    },
    async addTask(todo) {
      if (!this.newTask.trim()) return;

      console.log('addTaskメソッドが呼ばれました。');
      try {
        const response = await fetch('/task', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            goal_id: todo.id,
            task: this.newTask
          })
        });
        console.log(response);
        if (response.ok) {
          const task = await response.json();
          todo.tasks.push(task);
          this.newTask = '';  // フォームリセット
        } else {
          alert('タスクの追加に失敗しました');
        }
      } catch (error) {
        console.error('エラー:', error);
      }
    },
    async toggleTaskStatus(task) {
      // 状態をループ切り替え
      const nextStatus = this.getNextStatus(task.status);
      task.status = nextStatus;

      // APIでDBの状態を更新
      try {
        const response = await fetch(`/task/${task.id}/status`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ status: nextStatus })
        });

        if (!response.ok) {
          throw new Error('更新に失敗しました');
        }
      } catch (error) {
        console.error('エラー:', error);
        alert('ステータスの更新に失敗しました');
      }
    },
    getNextStatus(currentStatus) {
      const statusCycle = ['未着手', '進行中', '完了'];
      const currentIndex = statusCycle.indexOf(currentStatus);
      const nextIndex = (currentIndex + 1) % statusCycle.length;
      return statusCycle[nextIndex];
    },
    // 完了率を計算
    calculateCompletionRate(todo) {
      const totalTasks = todo.tasks.length;
      const completedTasks = todo.tasks.filter(task => task.status === '完了').length;
      return totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0;
    },

    // リアルタイムで完了率を更新
    updateCompletionRate(todo) {
      todo.completion_rate = this.calculateCompletionRate(todo);
    },
    statusClass(status) {
      return {
        'bg-gray-300 px-2 py-1 rounded': status === '未着手',
        'bg-yellow-300 px-2 py-1 rounded': status === '進行中',
        'bg-green-500 px-2 py-1 rounded text-white': status === '完了'
      };
    },
    async deleteTask(todoId, taskId) {
      const todo = this.todos.find(t => t.id === todoId);
      if (!todo) return;

      todo.tasks = todo.tasks.filter(task => task.id !== taskId);
      try {
        console.log(taskId);
        const response = await fetch(`task/${taskId}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        if (!response.ok) {
          alert('削除に失敗しました。');
        }
      } catch (error) {
        console.error('エラー:', error);
      }
    },
    async deleteTodo(id) {
      if (confirm('本当に削除しますか？')) {
        try {
          const response = await fetch(`/todo/${id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          });
          // console.log(response);
          if (response.ok) {
            this.todos = this.todos.filter(todo => todo.id !== id);
          } else {
            alert('削除に失敗しました');
          }
        } catch (error) {
          console.error('エラー:', error);
        }
      }
    },
    async updateOrder() {
      try {
        this.todos.forEach((todo, index) => {
          todo.order = index + 1;
        });
  
        const response = await fetch(`/todo/update-order`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
          },
          body: JSON.stringify({ order: this.todos.map(todo => ({ id: todo.id, order: todo.order })) }),
        });
  
        if (!response.ok) {
          throw new Error("順番の保存に失敗しました。");
        }

        console.log("順番が正常に保存されました");
      } catch (error) {
        console.error("エラー:", error);
        alert("順番の保存に失敗しました");
      }
    },
    editTodo(todo) {
      todo.isEditing = true;
      this.$nextTick(() => {
        const input = this.$refs.editInput;
        if (input && input.length > 0) {
          input[0].focus();
        }
      });
    },
    async finishEdit(todo) {
      todo.isEditing = false;
      try {
        const response = await fetch(`/todo/${todo.id}`, {
          method: 'PUT',
          headers: {
            "Content-Type": 'application/json',
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
          },
          body: JSON.stringify({ goal: todo.goal}),
        });
        console.log(response);

        if (!response.ok) {
          throw new Error('更新に失敗しました');
        }
      } catch (error) {
        console.error('エラー:', error);
        alert('更新に失敗しました');
      }
    },
    editTask(task) {
      task.isEditing = true;
      this.$nextTick(() => {
        const input = this.$refs.editInput;
        if (input && input.length > 0) {
          input[0].focus();
        }
      });
    },
    async finishEdit(task) {
      task.isEditing = false;
      try {
        const response = await fetch(`/task/${task.id}`, {
          method: 'PUT',
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
          },
          body: JSON.stringify({ task: todo.tasks.task}),
        });
        console.log(response);

        if (!response.ok) {
          throw new Error('更新に失敗しました');
        }

      }catch (error) {
        console.error('エラー:', error);
        alert('更新に失敗しました');
      }
    },

  }
};
</script>

<style scoped>
.cursor-pointer:hover {
  opacity: 0.8;
}
</style>
