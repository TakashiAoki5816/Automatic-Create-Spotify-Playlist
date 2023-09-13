<script setup lang='ts'>
import { reactive } from 'vue';
import axios, { AxiosResponse, AxiosError } from 'axios';
import { FormData } from './@types/index';
import MyPlaylist from '../../components/modules/MyPlaylist/MyPlaylist.vue';
import TextInput from '../../components/forms/formkit/TextInput.vue';
import GenreCategory from '../../components/modules/GenreCategory/GenreCategory.vue';

interface Playlists {
    id: number | null,
    name: string,
    images: {
        url: string,
        height: number,
        width: number,
    }[],
}

const formData: FormData = reactive({
    'target_playlist_ids': [],
    'playlist_name': '',
    'genres': [],
});

const createPlaylist = (): void => {
    if (confirm('プレイリストを作成しますか？')) {
        axios.post(<string>'/api/spotify/createPlaylist', <FormData>formData)
            .then(function (r: AxiosResponse) {
                console.log(r);
            })
            .catch(function (e: AxiosError) {
                console.log(e);
            });
    }
}
</script>

<template>
    <div class='w-1/2 mt-10 m-auto'>
        <FormKit
            type="form"
            :submit-attrs="{
                inputClass: 'mt-3 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800',
            }"
            @submit="createPlaylist"
        >
            <MyPlaylist
                v-model="formData.target_playlist_ids"
            />
            <TextInput
                label="プレイリスト名"
                name="playlist_name"
                validation="required"
                v-model="formData.playlist_name"
            />
            <GenreCategory
                v-model="formData.genres"
            />
        </FormKit>
    </div>
</template>

<style scoped>
.playlist-item {
    width: 350px;
    height: 350px;
    margin-left: 20px;
}
.playlist-item:nth-child(4n + 1) {
    margin-left: 0;
}
img {
    max-width: none;
}
</style>
./@types/index
