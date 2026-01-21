import { createRouter, createWebHistory } from "vue-router";
import { useAuth } from "./composables/useAuth";

import DashboardView from "./views/DashboardView.vue";
import DocumentsView from "./views/DocumentsView.vue";
import UploadView from "./views/UploadView.vue";
import LoginView from "./views/LoginView.vue";
import RegisterView from "./views/RegisterView.vue";

const routes = [
  { path: "/", redirect: "/dashboard" },

  { path: "/login", name: "login", component: LoginView, meta: { guestOnly: true } },
  { path: "/register", name: "register", component: RegisterView, meta: { guestOnly: true } },

  { path: "/dashboard", name: "dashboard", component: DashboardView, meta: { requiresAuth: true } },
  { path: "/documents", name: "documents", component: DocumentsView, meta: { requiresAuth: true } },
  { path: "/upload", name: "upload", component: UploadView, meta: { requiresAuth: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to) => {
  const auth = useAuth();

  // Po odświeżeniu: jeśli jest token, dociągnij usera raz.
  if (auth.isLoggedIn() && !auth.user.value) {
    await auth.fetchMe();
  }

  if (to.meta.requiresAuth && !auth.isLoggedIn()) {
    return { name: "login" };
  }

  if (to.meta.guestOnly && auth.isLoggedIn()) {
    return { name: "dashboard" };
  }
});

export default router;
