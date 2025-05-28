<template>
    <div id="app">
      <TopHeader />
      <LogoNavbar />
      <div class="container my-4">
        <div class="row">
          <!-- News Details Section -->
          <div class="col-lg-8 col-md-12">
            <NewsDetails />
          </div>
  
          <!-- Trending News Section (Visible only on Desktop) -->
          <div class="col-lg-4 mt-5 d-none d-lg-block">
            <TrendingNews />
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import TopHeader from "../components/TopHeader.vue";
  import LogoNavbar from "../components/LogoNavbar.vue";
  import NewsDetails from "../components/NewsDetails.vue";
  import TrendingNews from "../components/TrendingNewsTags.vue";
  
  export default {
    name: "NewsArticle",
    components: {
      TopHeader,
      LogoNavbar,
      NewsDetails,
      TrendingNews,
    },
  };
  </script>
  