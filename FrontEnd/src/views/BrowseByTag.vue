<template>
    <div id="app">
      <TopHeader />
      <LogoNavbar />
      <BrowseByTagC />
 
  
    </div>
  </template>
  
  <script>
  import TopHeader from "../components/TopHeader.vue";
  import LogoNavbar from "../components/LogoNavbar.vue";
import BrowseByTagC from "../components/BrowseByTagC.vue";
  
  export default {
    name: "BrowseByTag",
    components: {
      TopHeader,
      LogoNavbar,
      BrowseByTagC
    },
  };
  </script>
  