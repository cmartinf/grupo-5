<template>
  <div class="featured-news-section container-fluid py-3 mt-4">
    <div class="container-fluid">
      <!-- Header Section -->
      <div class="d-flex justify-content-between align-items-center mb-3 px-5">
        <h4 class="text-uppercase fw-bold">Noticias Destacadas</h4>
        <div class="d-flex align-items-center">
          <button class="btn btn-outline-secondary btn-sm me-2" @click="scrollLeft">
            <i class="bi bi-chevron-left"></i>
          </button>
          <button class="btn btn-outline-secondary btn-sm" @click="scrollRight">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>
      </div>

      <!-- News Items Section -->
      <div
        ref="newsContainer"
        class="d-flex overflow-hidden position-relative"
        style="scroll-snap-type: x mandatory;"
      >
        <div
          v-for="(item, index) in featuredNews"
          :key="index"
          class="card bg-dark text-white border-0 me-3"
          style="flex: 0 0 calc(20% - 1rem); scroll-snap-align: start; cursor: pointer;"
          @click="goToNews(item.id)"
        >
          <img
            :src="item.image"
            class="card-img no-radius"
            alt="Featured News"
          />
          <div class="card-img-overlay d-flex flex-column justify-content-end p-2 gradient-overlay">
            <span class="badge bg-warning text-dark mb-1 px-2 py-1">
              {{ item.category }}
            </span>
            <h6 class="card-title m-0 text-truncate">{{ item.title }}</h6>
            <p class="card-text small">{{ item.date }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "FeaturedNews",
  data() {
    return {
      featuredNews: [], // To hold dynamically fetched featured news
    };
  },
  mounted() {
    this.fetchFeaturedNews();
  },
  methods: {
    async fetchFeaturedNews() {
      const baseUrl = import.meta.env.VITE_APP_API_URL; // Base URL from .env
      try {
        const { data } = await axios.get(`${baseUrl}/api/news?limit=15`); // Fetch data from backend with limit=15
        const today = new Date().toLocaleDateString("en-US", {
          month: "short",
          day: "2-digit",
          year: "numeric",
        });

        const articles = data.map((item) => ({
          id: item.id || "nonews", // Add ID for navigation
          image: item.image
            ? `${baseUrl}/storage/${item.image}`
            : "../src/assets/demo.png", // Default image
          category: item.category || "General",
          title: item.title || "No News",
          date: item.published_at
            ? new Date(item.published_at).toLocaleDateString("en-US", {
                month: "short",
                day: "2-digit",
                year: "numeric",
              })
            : today,
        }));

        // Fill in missing items
        while (articles.length < 5) {
          articles.push({
            id: "nonews",
            image: "../src/assets/demo.png",
            category: "General",
            title: "No News",
            date: today,
          });
        }

        this.featuredNews = articles;
      } catch (error) {
        console.error("Error fetching featured news:", error);

        // Handle fallback if API fails
        const today = new Date().toLocaleDateString("en-US", {
          month: "short",
          day: "2-digit",
          year: "numeric",
        });

        this.featuredNews = Array(5).fill({
          id: "nonews",
          image: "../src/assets/demo.png",
          category: "General",
          title: "No News",
          date: today,
        });
      }finally{ this.$emit("loaded");}
     

    },
    scrollLeft() {
      const container = this.$refs.newsContainer;
      container.scrollBy({ left: -container.clientWidth, behavior: "smooth" });
    },
    scrollRight() {
      const container = this.$refs.newsContainer;
      container.scrollBy({ left: container.clientWidth, behavior: "smooth" });
    },
    goToNews(newsId) {
      if (newsId === "nonews") {
        window.open("https://nonews.org", "_blank"); // Open fallback link
      } else {
        this.$router.push(`/news/${newsId}`); // Navigate to the news detail page
      }
    },
  },
};
</script>

<style scoped>
.card-img {
  object-fit: cover;
  height: 200px;
}

.card-title {
  font-size: 0.9rem;
}

.card-text {
  font-size: 0.8rem;
}

.gradient-overlay {
  background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 50%, rgba(0, 0, 0, 0.8) 100%);
  border-radius: 0;
}
</style>
