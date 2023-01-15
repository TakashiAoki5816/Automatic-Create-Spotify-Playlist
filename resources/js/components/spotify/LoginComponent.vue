<template>
    <h1 class="text-red-500">Hello World</h1>
    <a href='#' v-on:click="spotifyLogin">Spotifyにログインする</a>
</template>
<script>
export default {
    methods: {
        spotifyLogin: function () {
            this.authorize();
        },
        authorize: function () {
            axios.get('api/spotify/authorizeUrl')
            .then(function (response) {
                window.location.href = response.data.url;
            })
            .catch(function (error) {
                console.log(error);
            });
        }
    }
}
</script>

