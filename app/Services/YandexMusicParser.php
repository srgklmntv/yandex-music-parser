<?php

namespace App\Services;

use App\Models\Artist;
use App\Models\Track;
use GuzzleHttp\Client;
use DOMDocument;
use DOMXPath;

class YandexMusicParser
{
    protected Client $client;

    /**
     * Constructor initializes HTTP client.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Fetches HTML content from a given URL.
     *
     * @param string $url The URL to fetch HTML from.
     * @return string The raw HTML content.
     * @throws \Exception If the request fails.
     */
    private function fetchHtml(string $url): string
    {
        try {
            $response = $this->client->get($url);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to fetch data from Yandex Music: HTTP ' . $response->getStatusCode());
            }
            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            throw new \Exception('Error fetching HTML: ' . $e->getMessage());
        }
    }

    /**
     * Parses and saves artist and track data to the database.
     *
     * @param int $artistId The ID of the artist to parse.
     * @return array Parsed data including artist details and track count.
     * @throws \Exception If parsing fails.
     */
    public function parseArtist(int $artistId): array
    {
        try {
            // Fetch artist's tracks page and albums page separately
            $artistPageHtml = $this->fetchHtml("https://music.yandex.ru/artist/{$artistId}/tracks");
            $albumsPageHtml = $this->fetchHtml("https://music.yandex.ru/artist/{$artistId}/albums");

            // Load HTML into DOM objects for parsing
            $artistDom = new DOMDocument();
            @$artistDom->loadHTML($artistPageHtml);
            $artistXpath = new DOMXPath($artistDom);

            $albumsDom = new DOMDocument();
            @$albumsDom->loadHTML($albumsPageHtml);
            $albumsXpath = new DOMXPath($albumsDom);

            // Extract artist details
            $artistName = $this->extractArtistName($artistXpath);
            if ($artistName === 'Unknown Artist') {
                throw new \Exception('Artist not found or invalid artist ID.');
            }

            $subscribers = $this->extractSubscribers($artistXpath);
            $monthlyListeners = $this->extractMonthlyListeners($artistXpath);
            $albumsCount = $this->extractAlbumsCount($albumsXpath);

            // Save or update artist data in the database
            $artist = Artist::updateOrCreate(
                ['name' => $artistName],
                [
                    'subscribers_count' => $subscribers,
                    'monthly_listeners' => $monthlyListeners,
                    'albums_count' => $albumsCount
                ]
            );

            // Extract and save or update tracks
            $tracks = $this->extractTracks($artistXpath);
            foreach ($tracks as $track) {
                Track::updateOrCreate(
                    ['name' => $track['name'], 'artist_id' => $artist->id],
                    ['duration' => $track['duration']]
                );
            }

            return [
                'artist' => $artistName,
                'subscribers' => $subscribers,
                'monthly_listeners' => $monthlyListeners,
                'albums_count' => $albumsCount,
                'tracks' => count($tracks)
            ];
        } catch (\Exception $e) {
            throw new \Exception('Error parsing artist: ' . $e->getMessage());
        }
    }

    /**
     * Extracts the artist's name.
     *
     * @param DOMXPath $xpath The XPath object for parsing.
     * @return string The artist's name or 'Unknown Artist' if not found.
     */
    private function extractArtistName(DOMXPath $xpath): string
    {
        $node = $xpath->query("//h1[contains(@class, 'page-artist__title')]");
        return $node->length ? trim($node->item(0)->nodeValue) : 'Unknown Artist';
    }

    /**
     * Extracts the number of subscribers.
     *
     * @param DOMXPath $xpath The XPath object for parsing.
     * @return int The number of subscribers.
     */
    private function extractSubscribers(DOMXPath $xpath): int
    {
        $node = $xpath->query("//span[contains(@class, 'd-like_theme-count')]");
        return $node->length ? (int)filter_var($node->item(0)->nodeValue, FILTER_SANITIZE_NUMBER_INT) : 0;
    }

    /**
     * Extracts the number of monthly listeners.
     *
     * @param DOMXPath $xpath The XPath object for parsing.
     * @return int The number of monthly listeners.
     */
    private function extractMonthlyListeners(DOMXPath $xpath): int
    {
        $node = $xpath->query("//div[contains(@class, 'page-artist__summary')]/span");
        return $node->length ? (int)filter_var($node->item(0)->nodeValue, FILTER_SANITIZE_NUMBER_INT) : 0;
    }

    /**
     * Extracts the number of albums from the artist's albums page.
     *
     * @param DOMXPath $xpath The XPath object for parsing.
     * @return int The number of albums.
     */
    private function extractAlbumsCount(DOMXPath $xpath): int
    {
        $nodes = $xpath->query("//div[contains(@class, 'album__title')]");
        return $nodes->length;
    }

    /**
     * Extracts track names and durations from the artist's tracks page.
     *
     * @param DOMXPath $xpath The XPath object for parsing.
     * @return array An array of tracks with names and durations.
     */
    private function extractTracks(DOMXPath $xpath): array
    {
        $trackNodes = $xpath->query("//a[contains(@class, 'd-track__title')]");
        $durationNodes = $xpath->query("//div[contains(@class, 'd-track__end-column')]");

        $tracks = [];
        foreach ($trackNodes as $index => $trackNode) {
            // Extract track name
            $name = trim($trackNode->nodeValue);

            // Extract and calculate track duration
            $durationParts = explode(':', trim($durationNodes->item($index)->nodeValue ?? '0:00'));
            $duration = ((int)$durationParts[0] * 60) + (int)$durationParts[1];

            // Append track data to the tracks array
            $tracks[] = ['name' => $name, 'duration' => $duration];
        }

        return $tracks;
    }
}