<template>
    <div>
        <a href="#" v-on:click="createPlaylist">プレイリストを作成する</a>
    </div>
</template>
<script>
export default {
    methods: {
        createPlaylist: function () {
            if (confirm('プレイリストを作成しますか？')) {
                axios.get('/api/spotify/createPlaylist')
                .then(function (r) {
                    console.log(r);
                })
                .catch(function (e) {
                    console.log(e);
                });
            }
        }
    }
}
</script>
