<script setup lang='ts'>
import { ref, onMounted, watch } from 'vue';
import axios, { AxiosResponse, AxiosError } from 'axios';

interface Emits {
    (e: "eventCheckedPlaylistIds", target_playlist_ids: Array<string>): void;
}

const emit = defineEmits<Emits>();
const myPlaylist: any = ref([]);
const checkedPlaylistIds = ref([]);

/**
 * マイプレイリストを取得
 * @return {void}
 */
const retrieveMyPlaylist = async () => {
    await axios.get('/api/spotify/myPlaylist')
        .then(function (r: AxiosResponse) {
            myPlaylist.value = r.data.items;
        })
        .catch(function (e: AxiosError) {
            console.log(e);
        });
}

// チェックしたプレイリストを監視し、親に値渡し
watch(checkedPlaylistIds , (newPlaylistIds: Array<string>, oldPlaylistIds: Array<string>) => {
    emit('eventCheckedPlaylistIds', checkedPlaylistIds.value);
});

onMounted(() => {
    retrieveMyPlaylist();
});
</script>
<template>
    <ul class="w-full flex flex-wrap">
        <li v-for="(playlist, i) in myPlaylist" :key="i" class="playlist-item">
            <input type="checkbox" name="target_playlist_ids" :value="playlist.id" v-model="checkedPlaylistIds">{{ playlist.name}}
            <img :src="playlist.images[1]?.url" :height="playlist.images[1]?.height" :width="playlist.images[1]?.width">
        </li>
    </ul>
</template>
