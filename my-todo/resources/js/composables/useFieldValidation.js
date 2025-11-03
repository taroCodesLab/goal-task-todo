import { ref, computed } from 'vue';


export function useFieldValidation(initialValue = '', rules = []) {
    const value = ref(initialValue);
    const error = ref('');

    const validate = () => {
        error.value = '';
        for (const rule of rules) {
            // 関数の実行、取得
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

//ルールをバリデーション関数として定義
export const rules = {
    required: (message) => {
        return (value) => {
            if (!value || value.trim() === '') {
                return message;
            }
            return null;
        };
    },
    
    maxLength: (max, message) => {
        return (value) => {
            if (value && value.length > max) {
                return message;
            }
            return null;
        };
    }
};