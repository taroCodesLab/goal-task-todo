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
import { provide } from "vue";
import draggable from "vuedraggable";
import { useI18n } from "vue-i18n";
import { useConfirm } from "../composables/useConfirm";
import ConfirmModal from "./ConfirmModal.vue";
import goalItem from "./GoalItem.vue";
import { useGoalStore } from '../stores/goalStore';
import { useFieldValidation, rules } from "../composables/useFieldValidation";
import { useItemLimit } from "../composables/useItemLimit";
import { useNotify } from "../composables/useNotify";
import { storeToRefs } from 'pinia';
import { onMounted } from "vue";


// props
const props = defineProps({
  initialGoals: Array,
  isAuthenticated: Boolean
});

const { t } = useI18n();
const goalStore = useGoalStore();
const { goals } = storeToRefs(goalStore);
const { isVisible, message, openConfirm, confirm, cancel} = useConfirm();
const { notifyError } = useNotify();

onMounted(async () => {
  try {
    if (!props.isAuthenticated) {
      goalStore.setMode('guest');
      try {
        goalStore.loadFromLocalStorage();
      } catch (e) {
        console.error('loadFromLocalStorage failed:', e);
        setTimeout(() => notifyError(e), 100);
      }
    } else {
      goalStore.setMode('guest');
      try {
        await goalStore.transferLocalGoalsToServer();
      } catch (e) {
        console.error('transferLocalGoalsToServer failed:', e);
        notifyError(e);
      } finally {
        goalStore.setMode('user');
        
        if (!Array.isArray(goalStore.goals) || goalStore.goals.length === 0) {
          goalStore.goals = Array.isArray(props.initialGoals) ? [...props.initialGoals] : [];
        }
      }

    }
  } catch (e) {
    console.error('GoalList initialization error:', e);

    notifyError(e);
  }
});


//Validations
const goalField = useFieldValidation('', [
  rules.required(t('validation.required')),
  rules.maxLength(255, t('validation.maxLength', { length: 255 }))
]);

const { isLimitReached, setLimitError } = useItemLimit({
  store: goalStore,
  maxCount: 3,
  countSelector: s => s.goals.length,
  modeSelector: s => s.mode
});

setLimitError(goalField, t('validation.guestLimit'));

// methods
const addGoal = async () => {

  if (isLimitReached.value) {
    goalField.setError(t('validation.guestLimit'));
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
    console.error('Operation failed:', e);
    notifyError(e || 'Failed to add goal');
  }
};

const deleteGoal = async (id) => {
  const confirmed = await openConfirm(t('confirm.message'));
  if (!confirmed) return;
  try {
    await goalStore.deleteGoal(id);
  } catch (e) {
    console.error('Operation failed:', e);
    notifyError(e);
  }
};

const updateOrder = async () => {
  try {
    await goalStore.updateOrder();
  } catch (e) {
    console.error('Operation failed:', e);
    notifyError(e);
  }
};

const updateGoal = async (updatedGoal) => {
  try {
    await goalStore.updateGoal(updatedGoal);
  } catch (e) {
    console.error('Operation failed:', e);
    notifyError(e);
  }
};

// task関係の関数(provideの使用)
const addTask = async (taskData) => {
  try {
    await goalStore.addTask(taskData.id, taskData.task);
  } catch (e) {
    console.error('Operation failed:', e);
    notifyError(e);
  }
};
provide('addTask', addTask);

const deleteTask = async (taskData) => {
  try {
    await goalStore.deleteTask(taskData.goal_id, taskData.id);
  } catch (e) {
    console.error('Operation failed:', e);
    notifyError(e);
  }
};
provide('deleteTask', deleteTask);

const toggleTaskStatus = async (nextStatus, id, goalId) => {
  try {
    await goalStore.updateTaskStatus(nextStatus, id, goalId);
  } catch (e) {
    console.error('Operation failed:', e);
    notifyError(e);
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
