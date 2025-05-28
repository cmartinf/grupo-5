<template>
    <div class="container my-5">
      <h2 class="text-center mb-4">Explorar noticias por etiqueta: "{{ tag }}"</h2>
  
      <!-- News List -->
      <div v-if="newsList.length" class="row g-4">
        <div v-for="news in newsList" :key="news.id" class="col-md-4">
          <div class="card border-0 shadow h-100" @click="goToNews(news.id)" style="cursor: pointer;">
            <img :src="news.image" class="card-img-top" alt="News Image" />
            <div class="card-body">
              <h5 class="card-title text-truncate text-dark">{{ news.title }}</h5>
              <div class="d-flex align-items-center mb-2">
                <img
                  :src="news.authorImage"
                  alt="Author Image"
                  class="rounded-circle me-2"
                  style="width: 30px; height: 30px; object-fit: cover;"
                />
                <p class="text-muted small mb-0">By {{ news.author }}</p>
              </div>
              <p class="text-muted small">{{ news.date }}</p>
            </div>
          </div>
        </div>
      </div>
  
      <!-- No News Fallback -->
      <div v-else class="text-center">
        <h5 class="text-muted">No hay noticias disponibles para esta etiqueta.</h5>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    name: "BrowseByTag",
    data() {
      return {
        tag: "",
        newsList: [], 
      };
    },
    async created() {
      this.tag = this.$route.query.tag;
      await this.fetchNewsByTag();
    },
    watch: {
     
      "$route.query.tag": "fetchNewsByTag",
    },
    methods: {
      async fetchNewsByTag() {
        try {
          const tag = this.$route.query.tag;
          const baseUrl = import.meta.env.VITE_APP_API_URL;
          const response = await axios.get(`${baseUrl}/api/news?tag=${tag}`);
          this.newsList = response.data.map((item) => ({
            id: item.id,
            title: item.title || "Untitled",
            image: `${baseUrl}/storage/${item.image || "default-image.jpg"}`,
            date: new Date(item.published_at).toLocaleDateString("en-US", {
              month: "short",
              day: "2-digit",
              year: "numeric",
            }),
            summary: (item.content || "").substring(0, 100) + "...",
            author: item.author?.name || "Unknown Author",
            authorImage: `${baseUrl}/storage/${item.author?.profile_picture || "default-avatar.png"}`,
          }));
        } catch (error) {
          console.error("Error fetching news by tag:", error);
          this.newsList = []; 
        }
      },
      goToNews(newsId) {
        this.$router.push(`/news/${newsId}`);
      },
    },
  };
  </script>
  
  <style scoped>
.container {
  color: white;
}

h2 {
  color: #1e3a8a;
  font-weight: bold;
}

.card {
  background-color: #1e3a8a;
  color: white;
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  border-radius: 0.5rem;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.card-img-top {
  object-fit: cover;
  height: 200px;
  border-top-left-radius: 0.5rem;
  border-top-right-radius: 0.5rem;
}

.card-title {
  font-size: 1.1rem;
  font-weight: bold;
  color: white;
}

.card-body {
  font-size: 0.9rem;
}

.text-truncate {
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.text-muted {
  color: #d1d5db !important;
}

.rounded-circle {
  border: 2px solid #3b82f6;
}
</style>
