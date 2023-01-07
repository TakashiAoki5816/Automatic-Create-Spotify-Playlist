import './bootstrap';
import '../css/app.css'

import { createApp } from "vue/dist/vue.esm-bundler";
import LoginComponent from "../js/components/spotify/LoginComponent.vue";
import AccessTokenComponent from './components/spotify/AccessTokenComponent.vue';
import CreatePlaylistComponent from './components/spotify/CreatePlaylistComponent.vue';

const app = createApp({
    components: {
        'login-component': LoginComponent,
        'access-token-component': AccessTokenComponent,
        'create-playlist-component': CreatePlaylistComponent,
    }
});
app.mount("#app");
