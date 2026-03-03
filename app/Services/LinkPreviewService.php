<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class LinkPreviewService
{
    private $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';

    public function generateThumbnailFromUrl($url)
    {
        try {
            Log::info('Generating thumbnail for URL: ' . $url);
            
            // Get HTML content - using withoutVerifying to handle local development SSL issues
            $response = Http::withHeaders(['User-Agent' => $this->userAgent])
                            ->withoutVerifying()
                            ->timeout(10)
                            ->get($url);
            
            if (!$response->successful()) {
                Log::warning('Failed to fetch URL for thumbnail: ' . $url . ' Status: ' . $response->status());
                return null;
            }

            $html = $response->body();

            // Try to get Open Graph image
            $ogImage = $this->extractMetaTag($html, 'og:image');
            if ($ogImage) {
                $ogImage = $this->makeAbsolute($ogImage, $url);
                Log::info('Found og:image: ' . $ogImage);
                return $this->downloadAndSaveImage($ogImage);
            }

            // Try to get Twitter card image
            $twitterImage = $this->extractMetaTag($html, 'twitter:image');
            if ($twitterImage) {
                $twitterImage = $this->makeAbsolute($twitterImage, $url);
                Log::info('Found twitter:image: ' . $twitterImage);
                return $this->downloadAndSaveImage($twitterImage);
            }

            // Try to find favicon or logo
            $icon = $this->extractFavicon($html, $url);
            if ($icon) {
                $icon = $this->makeAbsolute($icon, $url);
                Log::info('Found favicon: ' . $icon);
                return $this->downloadAndSaveImage($icon);
            }

            Log::info('No thumbnail image found for URL: ' . $url);
            return null;
            
        } catch (\Exception $e) {
            Log::error('Failed to generate thumbnail: ' . $e->getMessage());
            return null;
        }
    }

    private function extractMetaTag($html, $property)
    {
        $pattern = '/<meta[^>]*(property|name)=["\']' . preg_quote($property, '/') . '["\'][^>]*content=["\'][^"\']*["\'][^>]*>/i';
        
        if (preg_match($pattern, $html, $matches)) {
            if (preg_match('/content=["\']([^"\']+)["\']/i', $matches[0], $contentMatches)) {
                return $contentMatches[1];
            }
        }
        
        return null;
    }

    private function extractFavicon($html, $baseUrl)
    {
        // Try to find favicon link
        $patterns = [
            '/<link[^>]*rel=["\'](?:shortcut )?icon["\'][^>]*href=["\']([^"\']+)["\'][^>]*>/i',
            '/<link[^>]*href=["\']([^"\']+)["\'][^>]*rel=["\'](?:shortcut )?icon["\'][^>]*>/i',
            '/<meta[^>]*itemprop=["\']image["\'][^>]*content=["\']([^"\']+)["\'][^>]*>/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                return $matches[1];
            }
        }

        // Default favicon location
        $parsedUrl = parse_url($baseUrl);
        if (isset($parsedUrl['scheme']) && isset($parsedUrl['host'])) {
            return $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . '/favicon.ico';
        }
        
        return null;
    }

    private function makeAbsolute($url, $baseUrl)
    {
        if (strpos($url, 'http') === 0) {
            return $url;
        }

        $parsedUrl = parse_url($baseUrl);
        $base = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        
        if (strpos($url, '/') === 0) {
            return $base . $url;
        }
        
        return rtrim($base, '/') . '/' . ltrim($url, '/');
    }

    private function downloadAndSaveImage($imageUrl)
    {
        try {
            Log::info('Downloading image: ' . $imageUrl);
            
            $response = Http::withHeaders(['User-Agent' => $this->userAgent])
                            ->withoutVerifying()
                            ->timeout(10)
                            ->get($imageUrl);
            
            if (!$response->successful()) {
                Log::warning('Failed to download image: ' . $imageUrl . ' Status: ' . $response->status());
                return null;
            }

            $content = $response->body();
            if (empty($content)) {
                Log::warning('Empty image content for: ' . $imageUrl);
                return null;
            }

            // Generate unique filename
            $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
            $extension = $extension ?: 'jpg';
            // Sanitize extension
            if (strlen($extension) > 4 || !preg_match('/^[a-zA-Z0-9]+$/', $extension)) {
                $extension = 'jpg';
            }
            
            $filename = 'health_tips/' . Str::random(40) . '.' . $extension;

            // Ensure directory exists
            if (!Storage::disk('public')->exists('health_tips')) {
                Storage::disk('public')->makeDirectory('health_tips');
            }

            // Save to storage
            Storage::disk('public')->put($filename, $content);
            Log::info('Saved thumbnail to: ' . $filename);

            return $filename;
            
        } catch (\Exception $e) {
            Log::error('Failed to download image: ' . $e->getMessage());
            return null;
        }
    }

    public function extractMetadata($url)
    {
        try {
            Log::info('Extracting metadata for URL: ' . $url);
            
            $response = Http::withHeaders(['User-Agent' => $this->userAgent])
                            ->withoutVerifying()
                            ->timeout(10)
                            ->get($url);
            
            if (!$response->successful()) {
                Log::warning('Failed to fetch URL for metadata: ' . $url);
                return null;
            }

            $html = $response->body();

            // Extract Title
            $title = $this->extractMetaTag($html, 'og:title') ?: $this->extractMetaTag($html, 'twitter:title');
            if (!$title && preg_match('/<title>(.*?)<\/title>/is', $html, $matches)) {
                $title = trim($matches[1]);
            }

            // Extract Description
            $description = $this->extractMetaTag($html, 'og:description') ?: $this->extractMetaTag($html, 'twitter:description') ?: $this->extractMetaTag($html, 'description');

            // Extract Image URL
            $imageUrl = $this->extractMetaTag($html, 'og:image') ?: $this->extractMetaTag($html, 'twitter:image') ?: $this->extractFavicon($html, $url);
            if ($imageUrl) {
                $imageUrl = $this->makeAbsolute($imageUrl, $url);
            }

            return [
                'title' => html_entity_decode($title ?: 'Health Tip'),
                'description' => html_entity_decode($description ?: ''),
                'image_url' => $imageUrl ?: null,
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to extract metadata: ' . $e->getMessage());
            return null;
        }
    }
}