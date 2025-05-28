<template>
  <div class="featured-section container-fluid pb-4">
    <div class="row g-0 align-items-stretch">
      <!-- Main Carousel Section -->
      <div class="col-lg-7 h-100">
        <div
          v-if="slides.length"
          id="mainCarousel"
          class="carousel slide h-100"
          data-bs-ride="carousel"
        >
          <div class="carousel-inner h-100">
            <div
              v-for="(item, index) in slides"
              :key="index"
              :class="['carousel-item', { active: index === 0 }]"
              class="carousel-card-fixed-height"
            >
              <router-link :to="`/news/${item.id}`" class="card-link">
                <div class="card bg-dark text-white border-0 position-relative h-100">
                  <img
                    :src="item.image"
                    class="card-img h-100 w-100 no-radius"
                    alt="Main Featured"
                  />
                  <div class="card-img-overlay d-flex flex-column justify-content-end p-4 gradient-overlay">
                    <span class="badge bg-warning text-dark mb-2 px-3 py-2">{{ item.category }}</span>
                    <h4 class="card-title">{{ item.title }}</h4>
                    <p class="card-text small">{{ item.date }}</p>
                  </div>
                </div>
              </router-link>
            </div>
          </div>
          <!-- Carousel Controls -->
          <button
            class="carousel-control-prev"
            type="button"
            data-bs-target="#mainCarousel"
            data-bs-slide="prev"
          >
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
          </button>
          <button
            class="carousel-control-next"
            type="button"
            data-bs-target="#mainCarousel"
            data-bs-slide="next"
          >
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Próximo</span>
          </button>
        </div>
        <div v-else class="d-flex justify-content-center align-items-center h-100">
          <p class="text-white">No hay contenido destacado disponible.</p>
        </div>
      </div>

      <!-- Side Images Section -->
      <div class="col-lg-5 h-100">
        <div class="row g-0 h-100">
          <div
            v-for="(side, index) in sideImages"
            :key="index"
            class="col-6 col-lg-6"
          >
            <router-link :to="`/news/${side.id}`" class="card-link">
              <div class="card bg-dark text-white border-0 position-relative card-fixed-height fixed-image-height">
                <img
                  :src="side.image"
                  class="card-img fixed-image-height w-100 no-radius"
                  alt="Side Featured"
                />
                <div class="card-img-overlay d-flex flex-column justify-content-end p-2 gradient-overlay">
                  <span class="badge bg-warning text-dark mb-1 px-2 py-1">{{ side.category }}</span>
                  <h6 class="card-title m-0">{{ side.title }}</h6>
                  <p class="card-text small">{{ side.date }}</p>
                </div>
              </div>
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Breaking News Section -->
    <div class="bg-dark text-white d-flex justify-content-between align-items-center px-5 py-2 mt-0">
      <span class="badge bg-warning text-dark me-2 px-3 py-2">Últimas Noticias</span>
      <div class="flex-grow-1 text-truncate px-2">
        
      </div>
      <div class="d-flex align-items-center">
        <button class="btn btn-outline-light btn-sm me-1">
          <i class="bi bi-chevron-left"></i>
        </button>
        <button class="btn btn-outline-light btn-sm">
          <i class="bi bi-chevron-right"></i>
        </button>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";

export default {
  name: "FeaturedSection",
  data() {
    return {
      slides: [], // For main carousel slides
      sideImages: [], // For side image cards
      breakingNews: "", // Breaking news title
      breakingNewsId: null, // Breaking news ID
    };
  },
  mounted() {
    this.fetchFeaturedData();
  },
  methods: {
    async fetchFeaturedData() {
      const baseUrl = import.meta.env.VITE_APP_API_URL; // Get base URL from .env
      try {
        const { data } = await axios.get(`${baseUrl}/api/news?limit=7`); // Fetch data from backend with limit=7

        const today = new Date().toLocaleDateString("en-US", {
          year: "numeric",
          month: "short",
          day: "numeric",
        });

        const articles = data.map((item) => ({
          id: item.id, // Ensure the ID is included
          image: item.image
            ? `${baseUrl}/storage/${item.image}`
            : "../src/assets/demo.png", // Default image
          category: item.category || "General",
          title: item.title || "No News",
          date: item.published_at
            ? new Date(item.published_at).toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
              })
            : today, // Default date
        }));

        // Fill in missing items
        while (articles.length < 7) {
          articles.push({
            id: null,
            image: "../src/assets/demo.png",
            category: "General",
            title: "No News",
            date: today,
          });
        }

        // Assign to sections
        this.slides = articles.slice(0, 3); // Main carousel
        this.sideImages = articles.slice(3, 7); // Side images
        this.breakingNews = articles[0]?.title || "No breaking news available.";
        this.breakingNewsId = articles[0]?.id || null;
      } catch (error) {
        console.error("Error fetching featured data:", error);

        // Handle fallback if API fails
        const today = new Date().toLocaleDateString("en-US", {
          year: "numeric",
          month: "short",
          day: "numeric",
        });

        this.slides = Array(3).fill({
          id: null,
          image: "../src/assets/demo.png",
          category: "General",
          title: "No News",
          date: today,
        });

        this.sideImages = Array(4).fill({
          id: null,
          image: "../src/assets/demo.png",
          category: "General",
          title: "No News",
          date: today,
        });

        this.breakingNews = "Failed to fetch breaking news.";
        this.breakingNewsId = null;
      }finally{
        this.$emit("loaded");
      }
    },
  },
};
</script>
<style >
/* Set fixed height for carousel cards */
.carousel-card-fixed-height {
  height: 600px; /* Adjust height for consistency */
}

.card-fixed-height {
  height: 300px; /* Fixed height for side cards */
}

.fixed-image-height {
  object-fit: contain; /* Prevent image distortion */
}

.card-img {
  border-radius: 0 !important; object-fit: cover; /* Maintain aspect ratio and crop excess */
  width: 100%; /* Ensure the image fits the container's width */
  height: auto; /* Maintain natural height */
  max-height: 600px; /* Prevent the image from exceeding a maximum height */
}

.card-title {
  font-size: 1.1rem;
  font-weight: bold;
  color: white;
}

.card-text {
  font-size: 0.875rem;
  color: white;
}

.badge {
  font-size: 0.75rem;
}

.row.g-0 {
  gap: 0 !important;
}

.gradient-overlay {
  background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 70%, rgba(0, 0, 0, 0.7) 100%);
  border-radius: 0;
}

.bg-dark {
  background-color: #000 !important;
}

.container-fluid {
  padding: 0 !important;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
  filter: invert(1); /* Make controls visible on dark backgrounds */
}


</style>