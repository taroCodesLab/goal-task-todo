import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:81', // LaravelのURL
  withCredentials: true,            // Cookie を送信するために必要
  headers: {
    'X-Requested-With': 'XMLHttpRequest'
  }
});

export default api;