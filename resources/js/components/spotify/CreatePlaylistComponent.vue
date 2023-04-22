<script setup lang='ts'>
import { ref, reactive, watch } from 'vue';
import axios, { AxiosResponse, AxiosError } from 'axios';
import { FormData } from './@types/index';

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

const formData: FormData = reactive({
    'playlist_name': '',
    'genres': null,
});
</script>

<template>
    <div class='w-1/2 m-auto'>
        <FormKit
            type="form"
            :submit-attrs="{
                inputClass: 'mt-3 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800',
            }"
            @submit="createPlaylist"
        >
            <FormKit
                type="text"
                label="プレイリスト名"
                name="playlist_name"
                validation="required"
                label-class="block mb-2 text-sm text-gray-900 dark:text-white"
                input-class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"
                v-model="formData.playlist_name"
            />
            <FormKit
                type="checkbox"
                label="ジャンル"
                name="genres"
                :options="['Mushrooms', 'Olives', 'Salami', 'Anchovies']"
                validation="required|min:1"
                outer-class="mt-5"
                legend-class="block mb-2 text-sm text-gray-900 dark:text-white"
                wrapper-class="flex items-center mb-4"
                label-class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                input-class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                v-model="formData.genres"
            />
        </FormKit>
    </div>
</template>
