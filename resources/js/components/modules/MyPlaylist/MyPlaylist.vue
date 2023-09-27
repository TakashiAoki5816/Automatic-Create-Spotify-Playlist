<script setup lang='ts'>
import { ref, reactive, onMounted, computed } from 'vue';
import axios, { AxiosResponse, AxiosError } from 'axios';

interface Emits {
    (e: "eventTargetPlaylistIds", target_playlist_ids: Array<string>): void;
}

const playlists: any = ref([]);
const emit = defineEmits<Emits>();

const formData = reactive({
    'target_playlist_ids': [],
});

const computedTargetPlaylistIds = computed(() => {
    console.log('aaa');
    emit('eventTargetPlaylistIds', formData.target_playlist_ids);

    return formData.target_playlist_ids;
});

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
});
</script>
<!-- TODO computedがうまく機能しない時がある、eventがうまくいっていないのかも -->
<template>
    <ul class="w-full flex flex-wrap">
        <li v-for="(playlist, i) in playlists" :key="i" class="playlist-item">
            <input type="checkbox" name="target_playlist_ids" v-model="formData.target_playlist_ids" :value="playlist.id">{{ playlist.name}}
            <img :src="playlist.images[1]?.url" :height="playlist.images[1]?.height" :width="playlist.images[1]?.width">
        </li>
    </ul>
</template>
