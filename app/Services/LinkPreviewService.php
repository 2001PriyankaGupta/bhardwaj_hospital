<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class LinkPreviewService
{
    public function generateThumbnailFromUrl($url)
    {
        try {
            // Get HTML content
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return null;
            }

            $html = $response->body();

            // Try to get Open Graph image
            $ogImage = $this->extractMetaTag($html, 'og:image');
            
            if ($ogImage) {
                return $this->downloadAndSaveImage($ogImage);
            }

            // Try to get Twitter card image
            $twitterImage = $this->extractMetaTag($html, 'twitter:image');
            
            if ($twitterImage) {
                return $this->downloadAndSaveImage($twitterImage);
            }

            // Try to find favicon or logo
            $icon = $this->extractFavicon($html, $url);
            
            if ($icon) {
                return $this->downloadAndSaveImage($icon);
            }

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
                $iconUrl = $matches[1];
                
                // Convert relative URL to absolute
                if (strpos($iconUrl, 'http') !== 0) {
                    $parsedUrl = parse_url($baseUrl);
                    $base = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                    
                    if (strpos($iconUrl, '/') === 0) {
                        $iconUrl = $base . $iconUrl;
                    } else {
                        $iconUrl = rtrim($base, '/') . '/' . ltrim($iconUrl, '/');
                    }
                }
                
                return $iconUrl;
            }
        }

        // Default favicon location
        $parsedUrl = parse_url($baseUrl);
        return $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . '/favicon.ico';
    }

    private function downloadAndSaveImage($imageUrl)
    {
        try {
            $response = Http::timeout(10)->get($imageUrl);
            
            if (!$response->successful()) {
                return null;
            }

            // Generate unique filename
            $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
            $extension = $extension ?: 'jpg';
            $filename = 'health_tips/' . Str::random(40) . '.' . $extension;

            // Save to storage
            Storage::disk('public')->put($filename, $response->body());

            return $filename;
            
        } catch (\Exception $e) {
            Log::error('Failed to download image: ' . $e->getMessage());
            return null;
        }
    }
}