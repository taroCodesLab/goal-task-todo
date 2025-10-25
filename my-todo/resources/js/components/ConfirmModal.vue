<template>
  <div
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
    @click.self="$emit('cancel')"
  >
    <div
      class="bg-white p-6 rounded-lg shadow-lg text-center max-w-xs w-full"
      @click.stop
    >
      <p class="text-gray-800 text-base mb-6">{{ t('confirm.message') || message }}</p>

      <div class="flex justify-center space-x-4">
        <button
        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md text-gray-800 font-medium active:scale-95"
        @click="$emit('cancel')"
        >
        {{ t('confirm.cancel') }}
      </button>
      <button
        class="px-4 py-2 bg-red-500 hover:bg-red-600 rounded-md text-white font-medium active:scale-95"
        @click="$emit('confirm')"
      >
        {{ t('confirm.delete') }}
      </button> 
      </div>
    </div>
  </div>
</template>


<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
  isVisible: { type: Boolean, required: true },
  message: { type: String, required: true },
});

const emits = defineEmits(['confirm', 'cancel']);

// confirm / cancel イベントを親に通知
const confirm = () => emits('confirm');
const cancel = () => emits('cancel');
</script>

<style scoped>
/* 背景がコンテンツを覆うようにする */
.fixed {
  pointer-events: auto;
}

.bg-black {
  pointer-events: all;
}

/* モーダル内クリックをしっかり拾う */
button {
  cursor: pointer;
}
</style>
