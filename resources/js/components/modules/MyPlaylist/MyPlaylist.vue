<script setup lang='ts'>
import { ref, onMounted } from 'vue';
import axios, { AxiosResponse, AxiosError } from 'axios';

const playlists: any = ref([]);

/**
 * マイプレイリストを取得
 * @return {void}
 */
const retrieveMyPlaylist = async () => {
    await axios.get('/api/spotify/myPlaylist')
        .then(function (r: AxiosResponse) {
            playlists.value = r.data.items;
        })
        .catch(function (e: AxiosError) {
            console.log(e);
        });
}

onMounted(() => {
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
