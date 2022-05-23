import { createApp } from "vue";

document.getElementById("dashboard").innerHTML = `{{ msg }}`;

const app = createApp({
    data: () => ({
        msg: "Hello World!"
    })
});

app.mount("#dashboard");