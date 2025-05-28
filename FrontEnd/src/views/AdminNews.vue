<template>
    <div id="app">
      <TopHeader />
      <LogoNavbar />
      <AdminNewsC/>
  
    </div>
  </template>
  
  <script>
  import TopHeader from "../components/TopHeader.vue";
  import LogoNavbar from "../components/LogoNavbar.vue";
  import AdminNewsC from "@/components/AdminNewsC.vue";

  
  export default {
    name: "AdminNews",
    components: {
      TopHeader,
      LogoNavbar,
      AdminNewsC

    },
  };
  </script>
  