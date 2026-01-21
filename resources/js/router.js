import { createRouter, createWebHistory } from "vue-router";
import { getToken } from "./api";

import LoginView from "./views/LoginView.vue";
import DocumentsView from "./views/DocumentsView.vue";
import UploadView from "./views/UploadView.vue";

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: "/login", name: "login", component: LoginView },
    { path: "/", redirect: "/documents" },
    { path: "/documents", name: "documents", component: DocumentsView, meta: { auth: true } },
    { path: "/upload", name: "upload", component: UploadView, meta: { auth: true } },
  ],
});

router.beforeEach((to) => {
  if (to.meta.auth && !getToken()) return "/login";
  if (to.path === "/login" && getToken()) return "/documents";
});

export default router;
