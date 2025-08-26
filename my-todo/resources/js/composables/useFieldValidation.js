import { ref, computed } from 'vue';


export function useFieldValidation(initialValue = '', rules = []) {
    const value = ref(initialValue);
    const error = ref('');

    const validate = () => {
        error.value = '';
        for (const rule of rules) {
            const errorMessage = rule(value.value);
            if (errorMessage) {
                error.value = errorMessage;
                return;
            }
        }
    };

    const resetField = () => {
        value.value = initialValue;
        error.value = '';
    };

    const setValueSilently = (newValue) => {
        value.value = newValue;
    };

    const setError = (msg) => {
        error.value = msg ?? '';
    };

    const clearError = () => {
        error.value = '';
    }

    const fieldValue = computed({
        get: () => value.value,
        set: (newValue) => {
            value.value = newValue;
            validate();
        }
    });

    return {
        fieldValue,
        error,
        validate,
        resetField,
        setValueSilently,
        setError,
        clearError,
    };
}

export const rules = {
    required: (val) => !val ? 'この項目は必須です。' : null,
    maxLength: (len) => (val) => (val && val.length > len) ? `${len}文字以内で入力してください。` : null,
};