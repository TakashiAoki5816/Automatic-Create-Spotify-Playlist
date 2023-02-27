import './bootstrap';
import '../css/app.css'

import { createApp } from "vue/dist/vue.esm-bundler";
import AuthorizeComponent from "./components/spotify/AuthorizeComponent.vue";
import AccessTokenComponent from './components/spotify/AccessTokenComponent.vue';
import CreatePlaylistComponent from './components/spotify/CreatePlaylistComponent.vue';

const app = createApp({
    components: {
        'authorize-component': AuthorizeComponent,
        'access-token-component': AccessTokenComponent,
        'create-playlist-component': CreatePlaylistComponent,
    }
});
app.mount("#app");
