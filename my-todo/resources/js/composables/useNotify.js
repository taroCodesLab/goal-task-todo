// import { objectToString } from 'alpinejs';
import { useI18n } from 'vue-i18n';
import { useToast } from 'vue-toastification';


export function useNotify() {
    const toast = useToast();
    const { locale } = useI18n();

    const messages = {
        ja: {
            bad_request: '不正なリクエストです',
            unauthorized: '認証が必要です',
            forbidden: 'アクセスが拒否されました',
            not_found: '要求されたリソースが見つかりません',
            validation_error: '入力内容に誤りがあります',
            network_error: 'ネットワークエラーが発生しました',
            server_error: 'サーバーエラーが発生しました',
            limit_reached: 'ゴールの上限に達しています',
            import_failed: 'データのインポートに失敗しました',
            unknown_error: '予期せぬエラーが発生しました', 
        },
        en: {
            bad_request: 'Bad request',
            unauthorized: 'Authentication required',
            forbidden: 'Access is forbidden',
            not_found: 'Requested resource not found',
            validation_error: 'There are validation errors',
            network_error: 'A network error occurred',
            server_error: 'A server error occurred',
            limit_reached: 'Goal limit reached',
            import_failed: 'Data import failed',
            unknown_error: 'An unexpected error occurred',
        }
    };

    function getLocale() {
        return (locale && locale.value) ? locale.value.split('-')[0] : 'en';
    }

    function buildMessage(err) {
        const loc = getLocale();
        const dict = messages[loc] || messages.en;

        if (typeof err === 'string') return err;
        if (err instanceof Error && 'code' in err) {
            if (err.code === 'validation_error' && err.data?.errors) {
                const details = Object.values(err.data.errors).flat().join('\n');
                return details || (dict[err.code] || err.message);
            }
            return dict[err.code] || err.message || dict.unknown_error;
        }
        
        if (err && typeof err === 'object') {
            if (err.code) return dict[err.code] || err.message || dict.unknown_error;
            if (err.message) return err.message;
        }
        return dict.unknown_error;
    }

    const notifyError = (err) => {
        console.log('notifyError called with:', err);
        const message = buildMessage(err);

        if (toast && typeof toast.error === 'function') {
            toast.error(message, { timeout: 4000});
        } else {
            console.error('notifyError:', err);

            console.warn('User message:', message);
        }
    };

    return { notifyError };
}