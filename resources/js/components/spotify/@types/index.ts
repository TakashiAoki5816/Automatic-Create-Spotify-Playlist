export type Playlists = {
    id: number | null,
    name: string,
    images: {
        url: string,
        height: string,
        width: string,
    },
}

export type FormData = {
    target_playlist_ids: [],
    playlist_name: string,
    genres: [],
}
