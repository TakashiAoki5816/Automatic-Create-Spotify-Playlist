import './bootstrap';
import '../css/app.css'

import { createApp } from "vue/dist/vue.esm-bundler";
import LoginComponent from "../js/components/spotify/LoginComponent.vue";

const app = createApp({
    components: {
        'login-component': LoginComponent,
    }
});
app.mount("#app");
