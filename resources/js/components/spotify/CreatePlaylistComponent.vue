<template>
	<div>
		<a href="#" @click="createPlaylist">プレイリストを作成する</a>
	</div>
</template>

<script lang='ts'>
import { defineComponent } from 'vue';
import axios, { AxiosResponse, AxiosError } from 'axios';

export default defineComponent({
	setup() {
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

		return {
			createPlaylist,
		};
	},
});
</script>
