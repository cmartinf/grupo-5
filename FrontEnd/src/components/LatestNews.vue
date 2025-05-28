<template>
  <div class="latest-news">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="text-uppercase">
        <span class="border-start border-4 border-warning px-2"></span>Ultimas Noticias
      </h4>
      <router-link to="/news/category" class="text-decoration-none text-warning"> </router-link>
    </div>
    <div class="row g-4">
      <div v-for="news in latestNews" :key="news.id" class="col-md-6">
        <div class="card border-0 shadow" @click="goToNews(news.id)" style="cursor: pointer;">
          <img :src="news.image" class="card-img-top" alt="Imagen" />
          <div class="card-body">
            <span class="badge bg-warning text-dark mb-2">{{ news.category }}</span>
            <h5 class="card-title text-truncate text-black">{{ news.title }}</h5>
            <p class="text-muted small">{{ news.date }}</p>
           <p class="card-text small text-truncate" v-html="news.summary"></p>

            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <img
                  :src="news.author.image"
                  class="rounded-circle me-2"
                  alt="Author"
                  width="30"
                  height="30"
                />
                <small class="text-muted">{{ news.author.name }}</small>
              </div>
              <div>
                <small class="text-muted me-3"><i class="bi bi-eye"></i> {{ news.views }}</small>
                <small class="text-muted"><i class="bi bi-heart"></i> {{ news.hearts }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "LatestNews",
  data() {
    return {
      latestNews: [],
      baseUrl: import.meta.env.VITE_APP_API_URL,
    };
  },
  mounted() {
    this.fetchLatestNews();
  },
  methods: {
    async fetchLatestNews() {
      try {
        const { data } = await axios.get(
          `${this.baseUrl}/api/news?limit=4&sort_by=created_at&order=desc`
        );
        this.latestNews = this.processNewsData(data);
      } catch (error) {
        console.error("Error fetching latest news:", error);
      }finally{
        this.$emit("loaded");
      }
    },
    processNewsData(data) {
      return data.map((item) => ({
        id: item.id,
        image: `${this.baseUrl}/storage/${item.image}`,
        category: item.category || "General",
        title: item.title || "Untitled",
        date: new Date(item.published_at).toLocaleDateString("en-US", {
          month: "short",
          day: "2-digit",
          year: "numeric",
        }),
        summary: item.content.substring(0, 100) + "...",
        author: {
          name: item.author?.name || "Unknown",
          image: `${this.baseUrl}/storage/${item.author?.profile_picture || "default-avatar.png"}`,
        },
        views: item.views || 0,
        hearts: item.heart_counts || 0,
      }));
    },
    goToNews(newsId) {
      this.$router.push(`/news/${newsId}`);
    },
  },
};
</script>
<style scoped>
.card {
  background-color: #1e3a8a;
  color: white;
}

.card-img-top {
  object-fit: cover;
  height: 200px;
  border-radius: 0 !important;
}

.card-title {
  font-size: 0.9rem;
  font-weight: bold;
  color: white;
}

.card-text,
.text-muted {
  font-size: 0.8rem;
  color: #d1d5db !important;
}

.badge {
  background-color: #3b82f6;
  color: white;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 0.25rem;
}

h4 {
  font-weight: bold;
  color: #1e3a8a;
}

h5 {
  font-weight: bold;
}

.text-truncate {
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.rounded-circle {
  object-fit: cover;
}

a.text-warning {
  color: #3b82f6 !important;
}

a.text-warning:hover {
  color: #2563eb !important;
  text-decoration: underline;
}

.bi-eye,
.bi-heart {
  color: #e0e7ff;
}
</style>
