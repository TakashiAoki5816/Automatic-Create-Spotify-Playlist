<template>
    <a href='#' @click="authorizeSpotify">Spotifyにログインする</a>
</template>
<script lang='ts'>
import { defineComponent } from 'vue';
import axios from 'axios';

export default defineComponent({
    setup() {
        const authorizeSpotify = () => {
            axios.get('/api/spotify/authorization')
                .then(response => {
                    window.location.href = response.data.url;

                    // TODO ユーザー同意画面にて同意後にアクセストークンを取得できるようにする
                    // this.$nextTick(() => {
                    //     // 遷移先のページで行いたい処理
                    //     console.log('定数前');
                    //     const code = this.getParam('code');
                    //     console.log('定数ご' + code);
                    //     axios.get('api/spotify/getAccessToken?code=' + code)
                    //     .then(response2 => {
                    //         // 2つ目のAPIのレスポンスを受け取る
                    //     })
                    //     .catch(error => {
                    //         // エラーハンドリング
                    //     });
                    // });
                })
                .catch(error => {
                    // エラーハンドリング
                });
        };

        const getParam = (name, url) => {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            let regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        };

        return {
            authorizeSpotify,
            getParam,
        };
    },
});
</script>

