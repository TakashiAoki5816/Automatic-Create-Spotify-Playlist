<script setup lang='ts'>
import { ref, onMounted } from 'vue';
import axios, { AxiosResponse, AxiosError } from 'axios';

const playlists: any = ref([]);

/**
 * マイプレイリストを取得
 * @return {void}
 */
const retrieveMyPlaylist = async () => {
    try {
        const response: AxiosResponse = await axios.get('/api/spotify/myPlaylist');
        playlists.value = response.data.items;
    } catch (error) {
        console.log(error);
    }
}

onMounted(() => {
    console.log('Playlist mountedの中')
    retrieveMyPlaylist();
})
</script>
<template>
    <ul class="w-full flex flex-wrap">
        <li v-for="(playlist, i) in playlists" :key="i" class="playlist-item">
            <input type="checkbox" name="target_playlist_ids" :value="playlist.id">{{ playlist.name}}
            <img :src="playlist.images[1]?.url" :height="playlist.images[1]?.height" :width="playlist.images[1]?.width">
        </li>
    </ul>
</template>
<style></style>
