import { computed, watch } from "vue";

export function useItemLimit({ store, maxCount, countSelector, modeSelector}) {
    const isLimitReached = computed(() => {
        return modeSelector(store) === 'guest' && countSelector(store) >= maxCount;
    });

    const setLimitError = (field, message) => {
        watch(isLimitReached, (isLimited) => {
            field.error.value = isLimited ? message : '';
        }, {immediate: true });
    };

    return {
        isLimitReached,
        setLimitError,
    };
}