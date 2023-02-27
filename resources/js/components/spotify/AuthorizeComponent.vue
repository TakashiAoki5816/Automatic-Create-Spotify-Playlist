<template>
    <a href='#' v-on:click="authorizeSpotify">Spotifyにログインする</a>
</template>
<script>
export default {
    methods: {
        authorizeSpotify: function () {
            axios.get('api/spotify/authorization')
            .then(function (response) {
                window.location.href = response.data.url;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
    }
}
</script>

