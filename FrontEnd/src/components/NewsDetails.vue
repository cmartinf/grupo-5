<template>
    <div class="news-article" v-if="article">
      <!-- Image Section -->
      <div class="image-container">
        <img :src="`${baseUrl}/storage/${article.image}`" alt="News Image" class="article-image" />
      </div>
  
      <!-- Article Content -->
      <div class="content-container">
        <div class="meta-info">
          <span class="category" :class="`category-${article.category?.toLowerCase()}`">
            {{ article.category }}
          </span>
          <span class="date">{{ formatDate(article.published_at || article.created_at) }}</span>
        </div>
  
        <h1 class="title">{{ article.title }}</h1>
  
        <div class="author-share-container d-flex justify-content-between align-items-center">
          <div class="author-info d-flex align-items-center">
            <img
              :src="`${baseUrl}/storage/${article.author?.profile_picture}`"
              alt="Author"
              class="author-image"
            />
            <span class="author-name">{{ article.author?.name }}</span>
          </div>
          <div class="share-buttons">
            <button class="share-button facebook" @click="shareToFacebook">
              <i class="bi bi-facebook"></i>
            </button>
            <button class="share-button twitter" @click="shareToTwitter">
              <i class="bi bi-twitter"></i>
            </button>
            <button class="share-button copy-link" @click="copyLink">
              <i class="bi bi-clipboard"></i>
            </button>
          </div>
        </div>
  
        <div class="description" v-html="article.content"></div>
  
        <!-- Metrics Section -->
        <div class="metrics d-flex justify-content-end align-items-center gap-3">
          <span class="views">
            <i class="bi bi-eye-fill"></i> {{ article.views }}
          </span>
          <button class="heart-button" @click="addHeart">
            <i class="bi bi-heart-fill"></i> {{ article.heart_counts }}
          </button>
        </div>
      </div>
    </div>
    <div v-else class="loading">Loading...</div>
  </template>
  <script>
import axios from "axios";

export default {
  name: "NewsArticle",
  data() {
    return {
      article: null, // Holds the article data
      baseUrl: import.meta.env.VITE_APP_API_URL || "http://localhost", // API Base URL
    };
  },
  methods: {
    async fetchArticle() {
      try {
        const articleId = this.$route.params.id; // Get the article ID from the route
        const response = await axios.get(`${this.baseUrl}/api/news/${articleId}`); // Fetch from the backend
        this.article = response.data;
      } catch (error) {
        console.error("Error fetching the article:", error);
      }
    },
    async addHeart() {
      try {
        const articleId = this.article.id;
        this.article.heart_counts += 1; // Increment the heart count locally
        await axios.post(`${this.baseUrl}/api/news/${articleId}/heart`, {
          heart_counts: this.article.heart_counts,
        });
      } catch (error) {
        console.error("Error updating heart count:", error);
      }
    },
    formatDate(date) {
      const options = { year: "numeric", month: "short", day: "2-digit" };
      return new Date(date).toLocaleDateString(undefined, options);
    },
    shareToFacebook() {
      const url = `${window.location.href}`;
      const fbShareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
      window.open(fbShareUrl, "_blank");
    },
    shareToTwitter() {
      const url = `${window.location.href}`;
      const tweetText = `Check out this article: ${this.article.title}`;
      const twitterShareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(tweetText)}`;
      window.open(twitterShareUrl, "_blank");
    },
    copyLink() {
      const url = `${window.location.href}`;
      navigator.clipboard.writeText(url).then(() => {
        alert("Link copied to clipboard!");
      });
    },
  },
  mounted() {
    this.fetchArticle(); // Fetch article when the component is mounted
  },
};
</script>
<style scoped>
.news-article {
  max-width: 900px;
  margin: 40px auto;
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: box-shadow 0.3s;
}

.news-article:hover {
  box-shadow: 0 0.5rem 1.25rem rgba(0, 0, 0, 0.15);
}

.image-container {
  width: 100%;
  height: 300px;
  overflow: hidden;
}

.article-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.content-container {
  padding: 24px;
}

.meta-info {
  display: flex;
  align-items: center;
  margin-bottom: 12px;
  font-size: 14px;
  color: #777;
}

.meta-info .category {
  background-color: #0d6efd;
  color: #fff;
  padding: 4px 12px;
  border-radius: 50px;
  text-transform: uppercase;
  font-weight: bold;
  margin-right: 10px;
  font-size: 12px;
}

.meta-info .date {
  color: #999;
  font-size: 13px;
}

.title {
  font-size: 1.75rem;
  font-weight: bold;
  color: #333;
  margin-bottom: 20px;
}

.author-share-container {
  margin-bottom: 20px;
}

.author-info {
  display: flex;
  align-items: center;
}

.author-image {
  width: 40px;
  height: 40px;
  object-fit: cover;
  border-radius: 50%;
  margin-right: 10px;
}

.author-name {
  font-size: 15px;
  font-weight: 600;
  color: #444;
}

.share-buttons {
  display: flex;
  align-items: center;
  gap: 10px;
}

.share-button {
  border: none;
  background: none;
  font-size: 20px;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.share-button.facebook {
  color: #1877f2;
}

.share-button.twitter {
  color: #1da1f2;
}

.share-button.copy-link {
  color: #6c757d;
}

.share-button:hover {
  transform: scale(1.2);
}

.description {
  font-size: 16px;
  color: #555;
  line-height: 1.8;
  margin-top: 20px;
}

.metrics {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  margin-top: 30px;
  gap: 20px;
  font-size: 16px;
  color: #666;
}

.views i,
.heart-button i {
  margin-right: 6px;
}

.heart-button {
  border: none;
  background: none;
  color: #e74c3c;
  font-size: 16px;
  cursor: pointer;
  display: flex;
  align-items: center;
}

.loading {
  text-align: center;
  font-size: 18px;
  color: #888;
  margin: 40px 0;
}

/* Quill formatting overrides */
::v-deep(.ql-align-right) {
  text-align: right;
}

::v-deep(.ql-align-center) {
  text-align: center;
}

::v-deep(.ql-align-left) {
  text-align: left;
}

::v-deep(.ql-align-justify) {
  text-align: justify;
}

::v-deep(.ql-video) {
  width: 100%;
  height: auto;
  margin: 20px 0;
}

::v-deep(.ql-video iframe) {
  width: 100%;
  height: 450px;
  border: none;
}

</style>
