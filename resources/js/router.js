import { createRouter, createWebHistory } from "vue-router";

import LoginView from "./views/LoginView.vue";
import RegisterView from "./views/RegisterView.vue";
import DashboardView from "./views/DashboardView.vue";
import DocumentsView from "./views/DocumentsView.vue";
import UploadView from "./views/UploadView.vue";

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: "/", redirect: "/dashboard" },

    { path: "/login", component: LoginView },
    { path: "/register", component: RegisterView },

    { path: "/dashboard", component: DashboardView },
    { path: "/documents", component: DocumentsView },
    { path: "/upload", component: UploadView },

    { path: "/:pathMatch(.*)*", redirect: "/dashboard" },
  ],
});

export default router;
