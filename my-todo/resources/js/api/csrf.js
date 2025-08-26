import api from '../axios';

let csrfPromise = null;

export async function initCsrf() {
    if (!csrfPromise) {
        csrfPromise = api.get('/sanctum/csrf-cookie');
    }
    return csrfPromise;
}