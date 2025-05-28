<template>
    <div id="app">
      <TopHeader />
      <LogoNavbar />
      <NewsCategoryC />
 
  
    </div>
  </template>
  
  <script>
  import TopHeader from "../components/TopHeader.vue";
  import LogoNavbar from "../components/LogoNavbar.vue";
import NewsCategoryC from "../components/NewsCategoryC.vue";
  
  export default {
    name: "NewsCategory",
    components: {
      TopHeader,
      LogoNavbar,
      NewsCategoryC
    },
  };
  </script>
  