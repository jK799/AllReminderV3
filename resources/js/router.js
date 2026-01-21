import { createRouter, createWebHistory } from "vue-router";
import { getToken } from "./api";

import LoginView from "./views/LoginView.vue";
import DashboardView from "./views/DashboardView.vue";
import DocumentsView from "./views/DocumentsView.vue";
import UploadView from "./views/UploadView.vue";

const routes = [
  { path: "/", redirect: "/app" },

  { path: "/app/login", name: "login", component: LoginView },

  {
    path: "/app",
    name: "dashboard",
    component: DashboardView,
    meta: { requiresAuth: true },
  },
  {
    path: "/app/documents",
    name: "documents",
    component: DocumentsView,
    meta: { requiresAuth: true },
  },
  {
    path: "/app/upload",
    name: "upload",
    component: UploadView,
    meta: { requiresAuth: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to) => {
  const isLogged = !!getToken();

  if (to.meta?.requiresAuth && !isLogged) {
    return { name: "login" };
  }

  if (to.name === "login" && isLogged) {
    return { name: "dashboard" };
  }
});

export default router;
