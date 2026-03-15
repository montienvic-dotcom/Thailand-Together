<?php

namespace App\Services\ApiGateway\Adapters;

/**
 * Translation API adapter for multi-language support.
 * Separate from AI Agent for dedicated translation services
 * (e.g., Google Translate, DeepL).
 */
class TranslateAdapter extends BaseAdapter
{
    public function providerName(): string
    {
        return $this->config['provider_name'] ?? 'Translation Service';
    }

    /**
     * Translate text between languages.
     */
    public function translate(string $text, string $targetLang, ?string $sourceLang = null): mixed
    {
        return $this->execute('POST', '/translate', array_filter([
            'text' => $text,
            'target' => $targetLang,
            'source' => $sourceLang,
        ]));
    }

    /**
     * Translate multiple texts in batch.
     */
    public function translateBatch(array $texts, string $targetLang, ?string $sourceLang = null): mixed
    {
        return $this->execute('POST', '/translate/batch', array_filter([
            'texts' => $texts,
            'target' => $targetLang,
            'source' => $sourceLang,
        ]));
    }

    /**
     * Detect the language of given text.
     */
    public function detectLanguage(string $text): mixed
    {
        return $this->execute('POST', '/detect', [
            'text' => $text,
        ]);
    }

    /**
     * Get list of supported languages.
     */
    public function supportedLanguages(): mixed
    {
        return $this->execute('GET', '/languages');
    }

    protected function baseUrl(): string
    {
        return $this->credential?->credential('base_url')
            ?? $this->config['base_url']
            ?? '';
    }

    protected function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . ($this->credential?->credential('api_key') ?? ''),
            'Content-Type' => 'application/json',
        ];
    }
}
