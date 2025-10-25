<template>
  <div class="w-full flex flex-col items-center">
    <form @submit.prevent="addGoal" class="flex justify-center w-full max-w-xl">
        <div class="flex items-center space-x-2">
            <div class="mb-4">
                <label for="goal" class="block text-gray-700 text-sm font-bold mb-2">GOAL</label>
                <input type="text" name="goal" id="goal" v-model="goalField.fieldValue.value" placeholder="GOAL" class="shadow appearance-none border rounded w-64 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :disabled="isLimitReached">
                <p v-if="goalField.error.value" class="absolute top-115p left-34p text-red-500 text-xs italic mt-1">
                    {{ goalField.error.value }}
                </p>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded relative mt-3" :disabled="isLimitReached">
            <span class="text-2xl leading-none">+</span>
            </button>
        </div>
    </form>
    <div class="w-full flex justify-center items-center">
      <div class="w-full max-w-xl">
          <draggable v-model="goals" @end="updateOrder" item-key="id" tag="ul" class="mt-6 mx-2 space-y-3 flex flex-col jutify-center">
            <template #item="{ element: goal }">
              <goalItem :goal="goal" @delete-goal="deleteGoal" @update-goal="updateGoal" @toggle-details="onToggleDetails"/>
            </template>
          </draggable>
      </div>
    </div>
    <ConfirmModal v-if="isVisible" :isVisible="isVisible" :message="message" @confirm="confirm" @cancel="cancel" />
  </div>
</template>

<script setup>
import { ref, provide, watch, nextTick } from "vue";
import draggable from "vuedraggable";
import { useConfirm } from "../composables/useConfirm";
import ConfirmModal from "./ConfirmModal.vue";
import goalItem from "./GoalItem.vue";
import { useGoalStore } from '../stores/goalStore';
import { useFieldValidation, rules } from "../composables/useFieldValidation";
import { useItemLimit } from "../composables/useItemLimit";
import { storeToRefs } from 'pinia';
import { onMounted } from "vue";


// props
const props = defineProps({
  initialGoals: Array,
  isAuthenticated: Boolean
});

const newGoal = ref('');
const goalStore = useGoalStore();
const { goals, isGoalLimitedReached } = storeToRefs(goalStore);
const { isVisible, message, openConfirm, confirm, cancel} = useConfirm();

onMounted(() => {
  if (!props.isAuthenticated) {
    goalStore.setMode('guest');
    goalStore.loadFromLocalStorage();
  } else {
    goalStore.transferLocalGoalsToServer();

    goalStore.setMode('user');
    goalStore.goals = [...props.initialGoals];

  }
});

const limitReachedRule = (_) => {
  if (goalStore.isGoalLimitedReached) {
    return 'ゲストではこれ以上追加できません。';
  }
  return null;
}

//Validations
const goalField = useFieldValidation('', [
  rules.required,
  rules.maxLength(255),
]);

// watch(
//   isGoalLimitedReached,
//   (isLimited) => {
//     if (isLimited) {
//       goalField.setError('ゲストではこれ以上追加できません。');
//     } else {
//       console.log('reset');
//       goalField.clearError();
//     }
//   },
//   { immediate: true }
// );

const { isLimitReached, setLimitError } = useItemLimit({
  store: goalStore,
  maxCount: 3,
  countSelector: s => s.goals.length,
  modeSelector: s => s.mode
});

setLimitError(goalField, 'ゲストではこれ以上追加できません。');

// methods
const addGoal = async () => {

  if (isLimitReached.value) {
    goalField.setError('ゲストではこれ以上追加できません。');
    return;
  }

  goalField.validate();
  if (goalField.error.value) return;

  try {
    await goalStore.addGoal(goalField.fieldValue.value);

    goalField.setValueSilently('');

    if (!isLimitReached.value) {
      goalField.clearError();
    }
  } catch (e) {
    console.error('goal追加エラー:', e);
  }
};

const deleteGoal = async (id) => {
  const confirmed = await openConfirm('本当に削除しますか？');
  if (!confirmed) return;
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
    await goalStore.updateTaskStatus(nextStatus, id, goalId);
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
