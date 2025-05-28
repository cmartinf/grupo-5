<template>
    <div class="trending-news-tags ">
      <!-- Trending News -->
      <div class="mb-4">
        <h5 class="text-uppercase">
          <span class="border-start border-4 border-warning px-2"></span>Noticias Destacadas
        </h5>
        <ul class="list-unstyled mt-3">
          <li v-for="trend in trendingNews" :key="trend.id" class="d-flex mb-3 align-items-center">
            <router-link :to="`/news/${trend.id}`" class="d-flex text-decoration-none text-dark">
              <img :src="trend.image" class="rounded me-3" alt="Tendencia" width="80" height="80" />
              <div>
                <span class="badge bg-warning text-dark mb-1">{{ trend.category }}</span>
                <h6 class="mb-0 text-truncate">{{ trend.title }}</h6>
                <p class="text-muted small mb-0">{{ trend.date }}</p>
              </div>
            </router-link>
          </li>
        </ul>
      </div>
  
      <!-- Tags Section -->
      <div>
        <h5 class="text-uppercase">
          <span class="border-start border-4 border-warning px-2"></span>Temas
        </h5>
        <div class="mt-3">
          <router-link
            v-for="tag in tags"
            :key="tag"
            :to="`/browse/tag?tag=${tag}`"
            class="badge bg-light text-dark border me-2 mb-2 text-decoration-none"
          >
            {{ tag }}
          </router-link>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    name: "TrendingNewsTags",
    data() {
      return {
        trendingNews: [],
        tags: [],
        baseUrl: import.meta.env.VITE_APP_API_URL,
      };
    },
    mounted() {
      this.fetchTrendingNews();
      this.fetchTags();
    },
    methods: {
      async fetchTrendingNews() {
        try {
          const { data } = await axios.get(
            `${this.baseUrl}/api/news?limit=5&sort_by=views&order=desc`
          );
          this.trendingNews = this.processNewsData(data);
        } catch (error) {
          console.error("Error fetching trending news:", error);
        }
      },
      async fetchTags() {
        try {
          const { data } = await axios.get(`${this.baseUrl}/api/tags`);
          // Ensure tags is always an array of strings
          let tagsArray = [];
          if (Array.isArray(data)) {
            if (typeof data[0] === "string") {
              tagsArray = data;
            } else if (data[0] && typeof data[0].name === "string") {
              tagsArray = data.map((tag) => tag.name);
            }
          } else if (data && Array.isArray(data.tags)) {
            if (typeof data.tags[0] === "string") {
              tagsArray = data.tags;
            } else if (data.tags[0] && typeof data.tags[0].name === "string") {
              tagsArray = data.tags.map((tag) => tag.name);
            }
          }
          // Filter out any falsy or empty values
          this.tags = tagsArray.filter(tag => !!tag && tag.trim() !== "");
        } catch (error) {
          console.error("Error fetching tags:", error);
          this.tags = [];
        } finally {
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
        }));
      },
    },
  };
  </script>
  
  <style scoped>
.trending-news-tags {
  color: white;
}

h5 {
  font-weight: bold;
  color: #1e3a8a;
}

ul {
  padding-left: 0;
}

li {
  background-color: #1e3a8a;
  padding: 0.75rem;
  border-radius: 0.5rem;
}

.router-link-exact-active,
.router-link-active {
  color: white !important;
}

img.rounded {
  object-fit: cover;
}

.badge {
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 0.25rem;
}

.badge.bg-warning {
  background-color: #3b82f6 !important;
  color: white !important;
}

.badge.bg-light {
  background-color: #e0e7ff !important;
  color: #1e3a8a !important;
  border: 1px solid #cbd5e1;
  transition: all 0.2s ease-in-out;
}

.badge.bg-light:hover {
  background-color: #1e3a8a !important;
  color: white !important;
  border-color: #1e3a8a;
}

.text-truncate {
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.text-muted {
  color: #d1d5db !important;
  font-size: 0.8rem;
}

h6 {
  font-weight: bold;
  color: white;
}
</style>
