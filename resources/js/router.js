import { createRouter, createWebHistory } from "vue-router";
import { getToken, bootstrapAuth } from "./services/api";

import DashboardView from "./views/DashboardView.vue";
import DocumentsView from "./views/DocumentsView.vue";
import UploadView from "./views/UploadView.vue";
import LoginView from "./views/LoginView.vue";
import RegisterView from "./views/RegisterView.vue";

import VehicleDetailView from "./views/VehicleDetailView.vue";
import DeviceDetailView from "./views/DeviceDetailView.vue";

import NewVehicleView from "./views/NewVehicleView.vue";
import NewDeviceView from "./views/NewDeviceView.vue";

import VehicleCreateView from "./views/VehicleCreateView.vue";
import DeviceCreateView from "./views/DeviceCreateView.vue";
import ServiceCreateView from "./views/ServiceCreateView.vue";
import ReminderCreateView from "./views/ReminderCreateView.vue";


const routes = [
  { path: "/", redirect: "/dashboard" },

  { path: "/login", component: LoginView, meta: { guestOnly: true }, name: "login" },
  { path: "/register", component: RegisterView, meta: { guestOnly: true }, name: "register" },

  { path: "/dashboard", component: DashboardView, meta: { requiresAuth: true }, name: "dashboard" },
  { path: "/documents", component: DocumentsView, meta: { requiresAuth: true }, name: "documents" },
  { path: "/upload", component: UploadView, meta: { requiresAuth: true }, name: "upload" },

  { path: "/vehicles/:id", component: VehicleDetailView, meta: { requiresAuth: true }, name: "vehicle.show" },
  { path: "/devices/:id", component: DeviceDetailView, meta: { requiresAuth: true }, name: "device.show" },

  { path: "/vehicles/new", component: NewVehicleView, meta: { requiresAuth: true } },
{ path: "/devices/new", component: NewDeviceView, meta: { requiresAuth: true } },

{ path: "/vehicles/new", component: VehicleCreateView, meta: { requiresAuth: true } },
{ path: "/devices/new", component: DeviceCreateView, meta: { requiresAuth: true } },
{ path: "/services/new", component: ServiceCreateView, meta: { requiresAuth: true } },
{ path: "/reminders/new", component: ReminderCreateView, meta: { requiresAuth: true } },

];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

let bootstrapped = false;

router.beforeEach(async (to) => {
  if (!bootstrapped) {
    bootstrapped = true;
    await bootstrapAuth();
  }

  const hasToken = !!getToken();

  if (to.meta.requiresAuth && !hasToken) return "/login";
  if (to.meta.guestOnly && hasToken) return "/dashboard";

  return true;
});

export default router;
