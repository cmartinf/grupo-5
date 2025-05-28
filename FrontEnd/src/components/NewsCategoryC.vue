<template>
  <div class="container my-5">
    <!-- Encabezado de la página -->
    <div class="text-center mb-5">
      <h2 class="fw-bold text-dark">Noticias por Categorías</h2>
      <p class="text-muted">Explora las últimas noticias agrupadas por categorías.</p>
    </div>

    <div class="row">
      <!-- Barra lateral de categorías -->
      <div class="col-lg-2">
        <div class="sticky-top bg-light p-3 shadow-sm rounded">
          <h5 class="text-uppercase fw-bold mb-3 text-dark">Categorías</h5>
          <ul class="list-unstyled">
            <li
              v-for="category in sortedCategories"
              :key="category.name"
              class="mb-2"
            >
              <a
                href="#"
                class="text-decoration-none fw-bold link-primary"
                @click.prevent="scrollToCategory(category.name)"
              >
                {{ category.name }}
              </a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Noticias por Categorías -->
      <div class="col-lg-10">
        <div v-if="categorizedNews.length">
          <div
            v-for="category in sortedCategories"
            :key="category.name"
            :id="category.name"
            class="mb-5"
          >
            <h4 class="text-black border-start border-4 border-warning ps-3 mb-4">{{ category.name }}</h4>
            <div class="d-flex overflow-x-scroll scrollbar-hidden" :ref="`scrollable-${category.name}`" style="gap: 1rem;">
              <div
                v-for="news in category.articles"
                :key="news.id"
                class="card border-0 shadow-sm flex-shrink-0"
                style="width: 250px; cursor: pointer;"
                @click="goToNews(news.id)"
              >
                <img
                  :src="news.image"
                  class="card-img-top"
                  alt="Imagen de la noticia"
                  style="height: 150px; object-fit: cover;"
                />
                <div class="card-body">
                  <h5 class="card-title text-truncate text-black">{{ news.title }}</h5>
                  <p class="text-muted small text-black">Por {{ news.author }}</p>
                  <p class="text-muted small text-black">{{ news.date }}</p>
                </div>
              </div>
            </div>
            <!-- Flechas de navegación abajo -->
            <div class="d-flex justify-content-center mt-3">
              <button
                class="btn btn-primary mx-2"
                @click="scrollLeft(category.name)"
                aria-label="Desplazar a la izquierda"
              >
                <i class="bi bi-chevron-left"></i>
              </button>
              <button
                class="btn btn-primary mx-2"
                @click="scrollRight(category.name)"
                aria-label="Desplazar a la derecha"
              >
                <i class="bi bi-chevron-right"></i>
              </button>
            </div>
          </div>
        </div>
        <!-- Mensaje cuando no hay categorías -->
        <div v-else class="text-center">
          <h5 class="text-muted">No hay noticias disponibles en ninguna categoría.</h5>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "NewsByCategory",
  data() {
    return {
      categorizedNews: [], // Noticias agrupadas por categoría
    };
  },
  computed: {
    // Ordenar categorías alfabéticamente
    sortedCategories() {
      return this.categorizedNews.sort((a, b) => a.name.localeCompare(b.name));
    },
  },
  mounted() {
    this.fetchNewsByCategories();
  },
  methods: {
    async fetchNewsByCategories() {
      const baseUrl = import.meta.env.VITE_APP_API_URL;
      try {
        const { data } = await axios.get(`${baseUrl}/api/news-by-category`);
        // Formatear los datos en formato categorizado
        this.categorizedNews = Object.entries(data).map(([name, articles]) => ({
          name,
          articles: articles.map((item) => ({
            id: item.id,
            title: item.title || "Sin título",
            image: `${baseUrl}/storage/${item.image || "default-image.jpg"}`,
            date: new Date(item.published_at).toLocaleDateString("es-ES", {
              month: "short",
              day: "2-digit",
              year: "numeric",
            }),
            author: item.author?.name || "Autor desconocido",
          })),
        }));
      } catch (error) {
        console.error("Error al obtener noticias por categorías:", error);
      }
    },
    scrollToCategory(categoryName) {
      const element = document.getElementById(categoryName);
      if (element) {
        element.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    },
    scrollLeft(categoryName) {
      const scrollable = this.$refs[`scrollable-${categoryName}`][0];
      scrollable.scrollBy({ left: -300, behavior: "smooth" });
    },
    scrollRight(categoryName) {
      const scrollable = this.$refs[`scrollable-${categoryName}`][0];
      scrollable.scrollBy({ left: 300, behavior: "smooth" });
    },
    goToNews(newsId) {
      this.$router.push(`/news/${newsId}`);
    },
  },
};
</script>

<style scoped>
/* Fondo de la barra lateral */
.bg-light {
  background-color: #f8f9fa !important;
  border-radius: 8px;
}

.scrollbar-hidden {
  overflow-x: auto;
  scrollbar-width: none; /* Para Firefox */
}
.scrollbar-hidden::-webkit-scrollbar {
  display: none; /* Para Chrome, Safari y Edge */
}

.card-title {
  font-size: 1rem;
  font-weight: bold;
}

.link-primary {
  color: #0d6efd;
  text-decoration: underline;
}
.link-primary:hover {
  color: #004085;
}

.text-dark {
  color: #000 !important;
}

.btn-primary {
  background-color: #0d6efd !important;
  border: none;
}
</style>
