import { createApp } from "vue";
import { createPinia } from "pinia";
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';


import App from "./App.vue";
import router from "./router";


//lets use cdn
// Import Bootstrap CSS, JS, and Icons
// import "bootstrap-icons/font/bootstrap-icons.css";

import axios from "axios";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
// Axios global configuration
axios.defaults.baseURL = import.meta.env.VITE_APP_API_URL || "http://127.0.0.1:8000";
axios.interceptors.request.use((config) => {
  const token = localStorage.getItem("adminToken");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Create the Vue application
const app = createApp(App);

// Make Axios globally available
app.config.globalProperties.$axios = axios;

app.use(createPinia());
app.use(router);
app.use(Toast);

app.mount("#app");
