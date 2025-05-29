<template>
  <div class="top-header bg-header text-light py-2 d-none d-lg-block">
    <div class="d-flex justify-content-between align-items-center px-5">
      
      <div class="small datetime-style">
        {{ dateTime }}
      </div>

      <div class="small">
        <template v-if="!isLoggedIn">
          
          <!-- <button class="btn btn-login px-3 py-1" @click.prevent="redirectToLogin">
            <i class="bi bi-person-circle me-1"></i> Login
        </button>-->
        
      
      </template>
        <template v-else>
          <button
            class="btn btn-login px-3 py-1"
            @click="showModal"
          >
            <i class="bi bi-person-circle me-1"></i> Logout
          </button>
        </template>
      </div>
    </div>

    <div
      class="modal fade"
      id="logoutModal"
      tabindex="-1"
      aria-labelledby="logoutModalLabel"
      aria-hidden="true"
      ref="logoutModal"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-black" id="logoutModalLabel">Confirmar Logout</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="hideModal"
            ></button>
          </div>
          <div class="modal-body text-black">¿Estás seguro de que deseas cerrar sesión?</div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
              @click="hideModal"
            >
              Cancelar
            </button>
            <button type="button" class="btn btn-danger" @click="handleLogout">
              Salir
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from "bootstrap";
import axios from "axios";

export default {
  name: "TopHeader",
  data() {
    return {
      modalInstance: null,
      dateTime: this.getCurrentDateTime(),
      timer: null,
    };
  },
  computed: {
    isLoggedIn() {
      return !!localStorage.getItem("adminToken");
    },
  },
  mounted() {
    this.modalInstance = new Modal(this.$refs.logoutModal);

    this.timer = setInterval(() => {
      this.dateTime = this.getCurrentDateTime();
    }, 1000);
  },
  beforeUnmount() {
    clearInterval(this.timer);
  },
  methods: {
    getCurrentDateTime() {
      const now = new Date();
      return now.toLocaleString(undefined, {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
      });
    },
    redirectToLogin() {
      this.$router.push("/admin/login");
    },
    showModal() {
      if (this.modalInstance) {
        this.modalInstance.show();
      }
    },
    hideModal() {
      if (this.modalInstance) {
        this.modalInstance.hide();
      }
    },
    async handleLogout() {
      try {
        this.hideModal();
        await axios.post("api/admin/logout");
        localStorage.removeItem("adminToken");
        this.$router.push("/admin/login");
      } catch (error) {
        console.error(error);
        alert("Ups!!! Ocurrió un error al cerrar sesión.");
      }
    },
  },
};
</script>

<style scoped>
.top-header {
  width: 100%;
  background-color: var(--vt-c-indigo); /* Azul del tema */
}

/* Estilo para la fecha y hora en azul #007bff */
.datetime-style {
  color: #007bff;
  font-weight: 600;
  letter-spacing: 0.5px;
}

/* Botón login (y logout) estilizado igual */
.btn-login {
  background-color: #007bff;
  color: #fff;
  border-radius: 20px;
  border: none;
  transition: all 0.3s ease;
}

.btn-login:hover {
  background-color: #0056b3;
  transform: scale(1.05);
}
</style>




