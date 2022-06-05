import { createApp } from "vue";
import DashboardLayout from "./layouts/DashboardLayout.vue";

document.getElementById("dashboard").innerHTML = `<dashboard-layout></dashboard-layout>`;

const app = createApp({
    components: {
        DashboardLayout
    }
});
app.mount("#dashboard");