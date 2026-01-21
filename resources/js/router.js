import { createRouter, createWebHistory } from "vue-router";
import { getToken, bootstrapAuth } from "./services/api";

import DashboardView from "./views/DashboardView.vue";
import DocumentsView from "./views/DocumentsView.vue";
import UploadView from "./views/UploadView.vue";
import LoginView from "./views/LoginView.vue";
import RegisterView from "./views/RegisterView.vue";

const routes = [
  { path: "/", redirect: "/dashboard" },

  { path: "/login", component: LoginView, meta: { guestOnly: true } },
  { path: "/register", component: RegisterView, meta: { guestOnly: true } },

  { path: "/dashboard", component: DashboardView, meta: { requiresAuth: true } },
  { path: "/documents", component: DocumentsView, meta: { requiresAuth: true } },
  { path: "/upload", component: UploadView, meta: { requiresAuth: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

let bootstrapped = false;

router.beforeEach(async (to) => {
  // 1) odpal bootstrap tylko raz (po starcie / odświeżeniu)
  if (!bootstrapped) {
    bootstrapped = true;
    await bootstrapAuth();
  }

  const hasToken = !!getToken();

  // 2) jeśli route wymaga auth i nie ma tokenu -> login
  if (to.meta.requiresAuth && !hasToken) {
    return "/login";
  }

  // 3) jeśli guestOnly (login/register) i już jest token -> dashboard
  if (to.meta.guestOnly && hasToken) {
    return "/dashboard";
  }

  return true;
});

export default router;
