<template>
    <div>
        <a href="#" v-on:click='getAccessToken'>アクセストークンを発行</a>
    </div>
</template>
<script>
export default {
    setup() {

    },
    methods: {
        /**
         * Access Tokenを取得
         * @return {void}
         */
        getAccessToken: function () {
            if (confirm('アクセストークンを発行しますか？')) {
                const code = this.getParam('code');
                axios.get('api/spotify/getAccessToken?code=' + code)
                .then(function (r) {
                    console.log('アクセストークンを取得しました。')
                })
                .catch(function (e) {
                    console.log(e);
                });
            }
        },
        /**
         * 特定のキーのパラメーターを取得
         * @param {string} name
         * @param {string|null} url
         * @return {string}
         */
        getParam: function (name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            let regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }
    }
}
</script>
