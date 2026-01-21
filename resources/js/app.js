import { createApp } from "vue";
import router from "./router";
import AppLayout from "./components/AppLayout.vue";

createApp(AppLayout).use(router).mount("#app");
