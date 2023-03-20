<template>
	<div>
		<a href="#" @click="createPlaylist">プレイリストを作成する</a>
	</div>
</template>

<script lang='ts'>
import { defineComponent } from 'vue';
import axios from 'axios';

export default defineComponent({
	setup() {
		const createPlaylist = () => {
			if (confirm('プレイリストを作成しますか？')) {
				axios.get('/api/spotify/createPlaylist')
					.then(function (r) {
						console.log(r);
					})
					.catch(function (e) {
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
