<?php

namespace App\Http\Controllers;

use App\Services\YandexMusicParser;
use Illuminate\Http\JsonResponse;

class YandexMusicParserController extends Controller
{
    protected YandexMusicParser $parser;

    /**
     * YandexMusicParserController constructor.
     *
     * @param YandexMusicParser $parser
     */
    public function __construct(YandexMusicParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Parses artist data and saves it to the database.
     *
     * @param int $artistId
     * @return JsonResponse
     */
    public function parseArtist(int $artistId): JsonResponse
    {
        try {
            $result = $this->parser->parseArtist($artistId);
            return response()->json([
                'message' => 'Artist and tracks data successfully saved',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to parse artist data: ' . $e->getMessage()], 500);
        }
    }
}