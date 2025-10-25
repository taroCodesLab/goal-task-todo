import { ref } from 'vue';

export function useConfirm() {
    const isVisible = ref(false);
    const message = ref('');
    let resolveFn;

    const openConfirm = (msg) => {
        message.value = msg;
        isVisible.value = true;
        return new Promise((resolve) => {
            resolveFn = resolve;
        });
    };

    const confirm = () => {
        isVisible.value = false;
        resolveFn(true);
    };

    const cancel = () => {
        isVisible.value = false;
        resolveFn(false);
    };

    return { isVisible, message, openConfirm, confirm, cancel }
}