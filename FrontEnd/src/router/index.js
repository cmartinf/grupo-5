import { createRouter, createWebHistory } from "vue-router";

import HomeView from "../views/Home.vue";
import AdminLogin from "../views/AdminLogin.vue";
import AdminNews from "../views/AdminNews.vue";
import AdminAccounts from "../views/AdminAccounts.vue";
import NewsArticle from "../views/NewsArticle.vue";
import NewsCategory from "../views/NewsCategory.vue";
import BrowseByTag from "../views/BrowseByTag.vue";

const routes = [
  {
    path: "/",
    name: "home",
    component: HomeView,
  },
  {
    path: "/news/::id", // Dynamic route for a specific news article
    name: "NewsArticle",
    component: NewsArticle,
    props: true,
  },
  {
    path: "/news/category",
    name: "NewsCategory",
    component: NewsCategory,
  },
  {
    path: "/browse/tag",
    name: "BrowseByTag",
    component: BrowseByTag,
  },
  {
    path: "/admin/login",
    name: "AdminLogin",
    component: AdminLogin,
  },
  {
    path: "/admin/news",
    name: "AdminNews",
    component: AdminNews,
    meta: { requiresAuth: true }, // ✅ Ruta protegida
  },
  {
    path: "/admin/accounts",
    name: "AdminAccounts",
    component: AdminAccounts,
    meta: { requiresAuth: true }, // ✅ Ruta protegida
  },
  {
    path: "/:catchAll(.*)",
    redirect: "/", // ✅ Redirige al home si la ruta no existe
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

// ✅ Guardia de navegación global
router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem("adminToken");

  if (to.meta.requiresAuth && !isAuthenticated) {
    next("/"); // redirige al Home si no está logueado
  } else if (to.name === "AdminLogin" && isAuthenticated) {
    next("/admin/news"); // redirige al panel si ya está logueado
  } else {
    next(); // sigue con la navegación
  }
});

export default router;

