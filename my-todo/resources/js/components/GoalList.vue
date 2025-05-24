<template>
  <div class="w-full flex justify-center items-center">
    <div class="w-full max-w-xl">
        <draggable v-model="goals" @end="updateOrder" item-key="id" tag="ul" class="mt-6 space-y-3 flex flex-col jutify-center">
          <template #item="{ element: goal }">
            <goalItem :goal="goal" @delete-goal="deleteGoal" @update-goal="updateGoal" @toggle-details="onToggleDetails"/>
          </template>
        </draggable>
    </div>
  </div>
</template>

<script setup>
import { ref, provide } from "vue";
import draggable from "vuedraggable";
import goalItem from "./GoalItem.vue";
import { usegoalStore } from '../stores/goalStore';
import { storeToRefs } from 'pinia';
import { onMounted } from "vue";


// props
const props = defineProps({
  initialGoals: Array
});

const goalStore = usegoalStore();
const { goals } = storeToRefs(goalStore);

onMounted(() => {
  goalStore.goals = [...props.initialGoals];
});

// methods
const deleteGoal = async (id) => {
  if (!confirm('本当に削除しますか？')) return;
  try {
    await goalStore.deleteGoal(id);
  } catch (error) {
    console.error('エラー：', error);
  }
};

const updateOrder = async () => {
  try {
    await goalStore.updateOrder();
  } catch (error) {
    console.error('順番の保存に失敗しました', error);
  }
};

const updateGoal = async (updatedGoal) => {
  try {
    await goalStore.updateGoal(updatedGoal);
  } catch (error) {
    console.error('更新に失敗しました', error);
  }
};

// task関係の関数(provideの使用)
const addTask = async (taskData) => {
  try {
    await goalStore.addTask(taskData.id, taskData.task);
  } catch (error) {
    console.error('通信エラー：', error);
  }
};
provide('addTask', addTask);

const deleteTask = async (taskData) => {
  try {
    await goalStore.deleteTask(taskData.goal_id, taskData.id);
  } catch (error) {
    console.error('通信エラー：', error);
  }
};
provide('deleteTask', deleteTask);

const toggleTaskStatus = async (nextStatus, id, goalId) => {
  try {
    await goalStore.updateTaskStatus(goalId, id, nextStatus);
  } catch (error) {
    console.error('通信エラー：', error);
    alert('ステータスの更新に失敗しました');
  }
};
provide('toggleTaskStatus', toggleTaskStatus);

const onToggleDetails = (goalId) => {
  goalStore.toggleDetails(goalId);
};

</script>

<style scoped>
.cursor-pointer:hover {
  opacity: 0.8;
}
</style>
