import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import "./bootstrap"; // może być puste, ale zostawiamy
import "../css/app.css";

createApp(App).use(router).mount("#app");
