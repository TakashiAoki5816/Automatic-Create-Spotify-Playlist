import './bootstrap';
import '../css/app.css'

import { createApp } from "vue/dist/vue.esm-bundler";
import CreatePlaylistComponent from './feature/CreatePlaylist/CreatePlaylistComponent.vue';
import { plugin, defaultConfig } from '@formkit/vue'
import { createPinia } from "pinia";

const pinia = createPinia();

const createPlaylistApp = createApp({
    components: {
        'create-playlist-component': CreatePlaylistComponent,
    }
});
createPlaylistApp.use(plugin, defaultConfig);
createPlaylistApp.use(pinia);
createPlaylistApp.mount("#app");
