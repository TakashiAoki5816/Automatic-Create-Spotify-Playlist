import './bootstrap';
import '../css/app.css'

import { createApp } from "vue/dist/vue.esm-bundler";
import Home from './components/pages/Home.vue';
import { plugin, defaultConfig } from '@formkit/vue'
import { createPinia } from "pinia";

const pinia = createPinia();

const createPlaylistApp = createApp({
    components: {
        'home': Home,
    }
});
createPlaylistApp.use(plugin, defaultConfig);
createPlaylistApp.use(pinia);
createPlaylistApp.mount("#app");
