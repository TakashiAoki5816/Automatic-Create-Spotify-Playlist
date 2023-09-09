import './bootstrap';
import '../css/app.css'

import { createApp } from "vue/dist/vue.esm-bundler";
import CreatePlaylist from './feature/CreatePlaylist/CreatePlaylist.vue';
import { plugin, defaultConfig } from '@formkit/vue'
import { createPinia } from "pinia";

const pinia = createPinia();

const createPlaylistApp = createApp({
    components: {
        'create-playlist': CreatePlaylist,
    }
});
createPlaylistApp.use(plugin, defaultConfig);
createPlaylistApp.use(pinia);
createPlaylistApp.mount("#app");
