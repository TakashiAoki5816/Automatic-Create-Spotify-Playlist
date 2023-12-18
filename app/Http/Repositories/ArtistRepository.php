<?php

namespace App\Http\Repositories;

use App\Models\Artist;
use stdClass;

class ArtistRepository
{
    /**
     * アーティストIDからArtistモデルを取得
     *
     * @param string $artistId Spotify APIで設定されているアーティストID
     * @return Artist アーティスト
     */
    public function findByArtistId(string $artistId): ?Artist
    {
        return Artist::where(['artist_id' => $artistId])->first();
    }

    /**
     * アーティストIDからArtistモデルを取得
     *
     * @param stdClass $content
     * @return bool
     */
    public function save(stdClass $content): bool
    {
        $params = [
            'name' => $content->name,
            'genres' => json_encode($content->genres),
            'artist_id' => $content->id,
        ];

        $artist = new Artist();
        return $artist->fill($params)->save();
    }
}
