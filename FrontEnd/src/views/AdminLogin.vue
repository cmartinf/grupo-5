<template>
  <div class="login-container d-flex justify-content-center align-items-center vh-100" style="background-color: #001f3f;">
    <div class="login-box p-4 rounded shadow-lg w-100" style="max-width: 400px; background-color: #003366;">
      <!-- Logo/Title -->
      <div class="text-center mb-4">
        <h1 class="text-white">
          Manager<span class="text-primary">News</span>
        </h1>
      </div>

      <form @submit.prevent="handleLogin" novalidate>
        <div class="mb-3">
          <label for="email" class="form-label text-white">Correo Electrónico</label>
          <input
            type="email"
            v-model="email"
            id="email"
            class="form-control"
            placeholder="Ingrese su correo electrónico"
            required
            autocomplete="username"
          />
          <small v-if="error.email" class="text-danger">{{ error.email }}</small>
        </div>

        <div class="mb-3 position-relative">
          <label for="password" class="form-label text-white">Contraseña</label>
          <div class="input-group">
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="password"
              id="password"
              class="form-control"
              placeholder="Ingrese su contraseña"
              required
              autocomplete="current-password"
            />
            <button
              type="button"
              class="btn btn-outline-light"
              @click="togglePassword"
              :aria-label="showPassword ? 'Hide password' : 'Show password'"
            >
              <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
            </button>
          </div>
          <small v-if="error.password" class="text-danger">{{ error.password }}</small>
        </div>

        <button class="btn btn-primary w-100 fw-bold mb-2" type="submit">
         Ingresar
        </button>

        <small
          v-if="error.general"
          class="text-danger d-block mt-3 text-center"
          role="alert"
        >
          {{ error.general }}
        </small>
      </form>

      <!-- Footer -->
      <div class="text-center mt-4 small text-light">
        © 2025 Manager News
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "AdminLogin",
  data() {
    return {
      email: "",
      password: "",
      showPassword: false,
      error: {
        email: "",
        password: "",
        general: "",
      },
    };
  },
  methods: {
    togglePassword() {
      this.showPassword = !this.showPassword;
    },
    async handleLogin() {
      this.error = { email: "", password: "", general: "" };

      if (!this.email) {
        this.error.email = "Email is required.";
        return;
      }
      if (!this.password) {
        this.error.password = "Password is required.";
        return;
      }

      try {
        const response = await axios.post("api/admin/login", {
          email: this.email,
          password: this.password,
        });

        if (response.data.success) {
          localStorage.setItem("adminToken", response.data.token);
          this.$router.push("/");
        } else {
          this.error.general = response.data.message || "Login failed. Try again.";
        }
      } catch (error) {
        console.error(error);
        this.error.general =
          error.response?.data?.message || "Login failed. Please check your credentials.";
      }
    },

    goHome() {
      this.$router.push("/");
    },
  },
};
</script>

<style scoped>
.login-container {
  background-color: #001f3f;
}

.login-box {
  background-color: #003366;
  border-radius: 0.5rem;
}

.btn-primary {
  background-color: #007bff;
  border-color: #007bff;
}

.btn-primary:hover {
  background-color: #0056b3;
  border-color: #0056b3;
}
</style>
