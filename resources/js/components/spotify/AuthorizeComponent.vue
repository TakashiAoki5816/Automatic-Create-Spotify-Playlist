<template>
    <a href='#' v-on:click="authorizeSpotify">Spotifyにログインする</a>
</template>
<script>
export default {
    methods: {
        authorizeSpotify: function () {
            // axios.get('/api/spotify/authorization')
            //     .then(function (response) {
            //         window.location.href = response.data.url;
            //     })
            //     .catch(function (error) {
            //         console.log(error);
            //     });

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
        },
        /**
         * 特定のキーのパラメーターを取得
         * @param {string} name
         * @param {string|null} url
         * @return {string}
         */
        // getParam: function (name, url) {
        //     if (!url) url = window.location.href;
        //     name = name.replace(/[\[\]]/g, "\\$&");
        //     let regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        //         results = regex.exec(url);
        //     if (!results) return null;
        //     if (!results[2]) return '';
        //     return decodeURIComponent(results[2].replace(/\+/g, " "));
        // },
    }
}
</script>

