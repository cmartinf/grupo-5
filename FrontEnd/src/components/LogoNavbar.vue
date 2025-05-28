<template>
  <div>

    <div class="logo-section bg-white py-3 px-5">
      <div class="d-flex justify-content-between align-items-center">
        <div class="h1 logo-text text-center w-100 mb-0">
          Manager <span class="text-primary">News</span>
        </div>
        <div></div>
      </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-5">
      <div class="w-100">
        <button
          class="navbar-toggler"
          type="button"
          @click="toggleNavbar"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div
          class="collapse navbar-collapse"
          :class="{ show: isNavbarOpen }"
          id="navbarNav"
        >
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <router-link
                class="nav-link"
                :class="{ 'fw-bold text-light active': $route.path === '/' }"
                to="/"
              >
                Inicio
              </router-link>
            </li>
            <li class="nav-item">
              <router-link
                class="nav-link"
                :class="{ 'fw-bold text-light active': $route.path === '/news/category' }"
                to="/news/category"
              >
                Categorías
              </router-link>
            </li>
            <li class="nav-item dropdown" v-if="isAdminLoggedIn">
              <a
                class="nav-link dropdown-toggle"
                href="#"
                :class="{ 'fw-bold text-light active': $route.path.startsWith('/admin') }"
                role="button"
                @click.prevent="toggleDropdown"
                :aria-expanded="isDropdownOpen.toString()"
              >
                Administrador
              </a>
              <ul class="dropdown-menu" :class="{ show: isDropdownOpen }">
                <li>
                  <router-link class="dropdown-item" to="/admin/news">
                    Administrador de Noticias
                  </router-link>
                </li>
                <li><hr class="dropdown-divider" /></li>
                <li>
                  <a class="dropdown-item text-danger" @click="handleLogout">Salir</a>
                </li>
              </ul>
            </li>
          </ul>

          <form class="d-flex" @submit.prevent="searchByTag">
            <input
              v-model="searchQuery"
              class="form-control me-2"
              type="search"
              placeholder="Buscar Noticias"
              aria-label="Buscar"
            />
            <button class="btn btn-primary fw-bold px-3" type="submit">
              Buscar
            </button>
          </form>
        </div>
      </div>
    </nav>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "LogoNavbar",
  data() {
    return {
      isNavbarOpen: false,
      isDropdownOpen: false,
      searchQuery: "",
    };
  },
  computed: {
    isAdminLoggedIn() {
      return !!localStorage.getItem("adminToken");
    },
  },
  methods: {
    toggleNavbar() {
      this.isNavbarOpen = !this.isNavbarOpen;
    },
    toggleDropdown() {
      this.isDropdownOpen = !this.isDropdownOpen;
    },
 async handleLogout() {
  try {
    await axios.post("api/admin/logout");
    localStorage.removeItem("adminToken");
    console.log("Logout exitoso, redirigiendo a home");
    await this.$router.push("/admin/login");
  } catch (error) {
    console.error(error);
    alert("Ups!!! Ocurrió un error.");
  }
},

    searchByTag() {
      if (this.searchQuery.trim()) {
        this.$router.push({ path: "/browse/tag", query: { tag: this.searchQuery } });
      }
    },
  },
};
</script>

<style scoped>
.logo-section {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: white;
  padding: 2rem 3rem;
}

.logo-text {
  font-size: 3.5rem;
  font-weight: 800;
  color: #0b1f3a;
  user-select: none;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
}

.logo-text .text-primary {
  color: #007bff !important;
}

.navbar {
  width: 100%;
  background-color: #0b1f3a !important;
}

.nav-link {
  color: rgba(255, 255, 255, 0.85);
  transition: color 0.3s ease;
}

.nav-link:hover,
.nav-link.active,
.nav-link.fw-bold {
  color: #ffffff !important;
  font-weight: 600;
}

/* Dropdown menu */
.dropdown-menu {
  display: none;
  position: absolute;
  background-color: white;
  border-radius: 0.25rem;
  box-shadow: 0 0.5rem 1rem rgb(0 0 0 / 0.15);
  min-width: 10rem;
  z-index: 1000;
  padding: 0.5rem 0;
}

.dropdown-menu.show {
  display: block;
}

.dropdown-item {
  color: #212529;
  padding: 0.25rem 1.5rem;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.dropdown-item:hover {
  background-color: #f8f9fa;
}

.dropdown-divider {
  margin: 0.5rem 0;
  border-top: 1px solid #dee2e6;
}

/* Botón de búsqueda con estilo tipo login */
.btn-primary {
  background-color: #007bff;
  border-color: #007bff;
}

.btn-primary:hover {
  background-color: #0069d9;
  border-color: #0062cc;
}

.btn-primary:focus {
  box-shadow: 0 0 0 0.2rem rgba(0,123,255,.5);
}
</style>

