<?php

namespace AshAllenDesign\FaviconFetcher\Drivers;

use AshAllenDesign\FaviconFetcher\Concerns\HasDefaultFunctionality;
use AshAllenDesign\FaviconFetcher\Concerns\ValidatesUrls;
use AshAllenDesign\FaviconFetcher\Contracts\Fetcher;
use AshAllenDesign\FaviconFetcher\Exceptions\FaviconNotFoundException;
use AshAllenDesign\FaviconFetcher\Exceptions\InvalidUrlException;
use AshAllenDesign\FaviconFetcher\FetchedFavicon;
use Illuminate\Support\Facades\Http;

class FaviconKitDriver implements Fetcher
{
    use ValidatesUrls;
    use HasDefaultFunctionality;

    private const BASE_URL = 'https://api.faviconkit.com/';

    /**
     * @param string $url
     * @return FetchedFavicon|null
     * @throws InvalidUrlException
     */
    public function fetch(string $url): ?FetchedFavicon
    {
        if (! $this->urlIsValid($url)) {
            throw new InvalidUrlException($url.' is not a valid URL');
        }

        if ($this->useCache && $favicon = $this->attemptToFetchFromCache($url)) {
            return $favicon;
        }

        $urlWithoutProtocol = str_replace(['https://', 'http://'], '', $url);

        $faviconUrl = self::BASE_URL.$urlWithoutProtocol;

        $response = Http::get($faviconUrl);

        return $response->successful()? new FetchedFavicon($url, $faviconUrl, $this) : $this->notFound($url);
    }
}
