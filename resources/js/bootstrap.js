import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Interceptor global para descifrar errores de API/Axios en la consola del navegador
window.axios.interceptors.response.use(
    (response) => response,
    (error) => {
        const status = error.response ? error.response.status : 'RED/DESCONOCIDO';
        const data = error.response ? error.response.data : error.message;
        console.error(`[XamaSafe API Error - Status ${status}]:`, data);
        return Promise.reject(error);
    }
);

