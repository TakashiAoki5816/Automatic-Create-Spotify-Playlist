import './bootstrap';
import '../css/app.css'

import { createApp } from "vue/dist/vue.esm-bundler";
import AuthorizeComponent from "./components/spotify/AuthorizeComponent.vue";
import CreatePlaylistComponent from './components/spotify/CreatePlaylistComponent.vue';

const app = createApp({
    components: {
        'authorize-component': AuthorizeComponent,
        'create-playlist-component': CreatePlaylistComponent,
    }
});
app.mount("#app");
