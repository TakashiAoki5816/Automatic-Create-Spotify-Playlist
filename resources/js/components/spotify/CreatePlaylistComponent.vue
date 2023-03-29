<script setup lang='ts'>
import axios, { AxiosResponse, AxiosError } from 'axios';

const createPlaylist = (): void => {
    if (confirm('プレイリストを作成しますか？')) {
        axios.get('/api/spotify/createPlaylist')
            .then(function (r: AxiosResponse) {
                console.log(r);
            })
            .catch(function (e: AxiosError) {
                console.log(e);
            });
    }
};
</script>

<template>
	<div>
		<a href="#" @click="createPlaylist">プレイリストを作成する</a>
	</div>
</template>
