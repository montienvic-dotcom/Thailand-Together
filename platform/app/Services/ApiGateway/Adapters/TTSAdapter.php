<?php

namespace App\Services\ApiGateway\Adapters;

/**
 * Text-to-Speech adapter for voice synthesis services.
 * Separate from AI Agent for dedicated TTS providers
 * (e.g., Google Cloud TTS, Amazon Polly).
 */
class TTSAdapter extends BaseAdapter
{
    public function providerName(): string
    {
        return $this->config['provider_name'] ?? 'TTS Service';
    }

    /**
     * Synthesize speech from text.
     */
    public function synthesize(string $text, string $language = 'th', array $options = []): mixed
    {
        return $this->execute('POST', '/synthesize', array_merge([
            'text' => $text,
            'language' => $language,
        ], $options));
    }

    /**
     * Get available voices for a language.
     */
    public function listVoices(?string $language = null): mixed
    {
        return $this->execute('GET', '/voices', array_filter([
            'language' => $language,
        ]));
    }

    /**
     * Synthesize speech with SSML markup.
     */
    public function synthesizeSSML(string $ssml, string $language = 'th', array $options = []): mixed
    {
        return $this->execute('POST', '/synthesize', array_merge([
            'ssml' => $ssml,
            'language' => $language,
            'input_type' => 'ssml',
        ], $options));
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
