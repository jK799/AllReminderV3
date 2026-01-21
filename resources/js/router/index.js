import { createRouter, createWebHistory } from "vue-router";
import Login from "../views/Login.vue";
import Documents from "../views/Documents.vue";

function isAuthed() {
  return !!localStorage.getItem("token");
}

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: "/login", name: "login", component: Login },
    { path: "/", redirect: "/documents" },
    { path: "/documents", name: "documents", component: Documents, meta: { auth: true } },
  ],
});

router.beforeEach((to) => {
  if (to.meta.auth && !isAuthed()) return { name: "login" };
  if (to.name === "login" && isAuthed()) return { name: "documents" };
});

export default router;
