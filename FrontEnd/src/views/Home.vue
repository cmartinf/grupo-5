<template>
  <div id="app">
    <TopHeader />
    <LogoNavbar />

    <!-- Global Loading Spinner -->
    <div v-if="isLoading" class="loading-container d-flex justify-content-center align-items-center">
      <div class="spinner-border text-warning" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- Main Content -->
    <div v-show="!isLoading" class="content-container">
      <FeaturedSection @loaded="onChildLoaded" />
      <FeaturedNews @loaded="onChildLoaded" />
      <div class="row mt-4 px-2">
        <div class="col-lg-8">
          <LatestNews @loaded="onChildLoaded" />
        </div>
        <div class="col-lg-4">
          <TrendingNewsTags @loaded="onChildLoaded" />
        </div>
      </div>
      <Footer />
    </div>
  </div>
</template>

<script>
import TopHeader from "../components/TopHeader.vue";
import LogoNavbar from "../components/LogoNavbar.vue";
import FeaturedSection from "../components/FeaturedSection.vue";
import FeaturedNews from "../components/FeaturedNews.vue";
import LatestNews from "../components/LatestNews.vue";
import TrendingNewsTags from "../components/TrendingNewsTags.vue";
import Footer from "../components/Footer.vue";

export default {
  name: "Home",
  components: {
    TopHeader,
    LogoNavbar,
    FeaturedSection,
    FeaturedNews,
    LatestNews,
    TrendingNewsTags,
    Footer,
  },
  data() {
    return {
      isLoading: true, // Loading state for the entire app
      childrenLoaded: 0, // Tracks how many children have reported "loaded"
      totalChildren: 4, // Number of children expected to emit "loaded"
    };
  },
  methods: {
    onChildLoaded() {
      this.childrenLoaded++;
      if (this.childrenLoaded === this.totalChildren) {
        this.isLoading = false; // Finish loading when all children have loaded
      }
    },
  },
};
</script>

<style scoped>
/* Estilo global */
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f4f4f4;
  margin: 0;
  padding: 0;
  scroll-behavior: smooth;
}

/* Contenedor de carga (pantalla completa) */
.loading-container {
  height: 100vh;
  background-color: #ffffff;
  display: flex;
  justify-content: center;
  align-items: center;
  transition: opacity 0.3s ease-in-out;
}

/* Personalización del spinner */
.spinner-border {
  width: 3.5rem;
  height: 3.5rem;
  border-width: 0.4em;
  color: #f39c12;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Contenedor del contenido principal */
.content-container {
  max-width: 1200px;
  margin: 20px auto;
  padding: 0 15px;
  transition: opacity 0.5s ease-in-out;
}

/* Fila principal de secciones */
.row {
  margin: 0 -15px;
}

/* Columnas con espaciado interno */
.col-lg-8,
.col-lg-4 {
  padding: 0 15px;
}

/* Transición cuando cambia el estado de carga */
[v-cloak] {
  display: none;
}

</style>
