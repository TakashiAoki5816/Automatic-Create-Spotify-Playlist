import './bootstrap';
import '../css/app.css'

import { createApp } from "vue/dist/vue.esm-bundler";
import CreatePlaylistComponent from './components/CreatePlaylist/CreatePlaylistComponent.vue';
import { plugin, defaultConfig } from '@formkit/vue'

const createPlaylistApp = createApp({
    components: {
        'create-playlist-component': CreatePlaylistComponent,
    }
});
createPlaylistApp.use(plugin, defaultConfig);
createPlaylistApp.mount("#app");
