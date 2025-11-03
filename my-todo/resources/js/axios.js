import axios from 'axios'

const errorCodeMap = {
  400: 'bad_request',
  401: 'unauthorized',
  403: 'forbidden',
  404: 'not_found',
  422: 'validation_error',
  429: 'too_many_requests',
  500: 'server_error',
  503: 'service_unavailable'
};

export class ApiError extends Error {
  constructor(message, code, data = null) {
    super(message);
    this.name = 'ApiError';
    this.code = code;
    this.data = data;
  }
}

const api = axios.create({
  baseURL: 'http://localhost:81',
  withCredentials: true,
  headers: {
    'X-Requested-With': 'XMLHttpRequest'
  }
});

api.interceptors.response.use(
  response => response,
  error => {
    let apiError;

    if (error.response) {
      const status = error.response.status;
      const data = error.response.data;

      apiError = new ApiError(
        data.message || 'サーバーエラーが発生しました',
        data.code || errorCodeMap[status] || 'unknown_error',
        data
      );
    } else if (error.request) {
      apiError = new ApiError(
        'サーバーに接続できません',
        'network_error'
      );
    } else {
      apiError = new ApiError(
        error.message || '予期せぬエラーが発生しました',
        'client_error'
      );
    }

    return Promise.reject(apiError);
  }
);

export default api;