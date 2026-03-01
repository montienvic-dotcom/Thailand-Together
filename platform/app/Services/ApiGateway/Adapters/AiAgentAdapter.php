<?php

namespace App\Services\ApiGateway\Adapters;

/**
 * AI Agent adapter covering:
 * - AI Call Center
 * - Chatbot
 * - Translation
 * - Text-to-Speech
 */
class AiAgentAdapter extends BaseAdapter
{
    public function providerName(): string
    {
        return $this->config['provider_name'] ?? 'AI Agent';
    }

    /**
     * Send a chat/conversation message.
     */
    public function chat(string $message, array $context = []): mixed
    {
        return $this->execute('POST', '/chat', [
            'message' => $message,
            'context' => $context,
        ]);
    }

    /**
     * Translate text between languages.
     */
    public function translate(string $text, string $from, string $to): mixed
    {
        return $this->execute('POST', '/translate', [
            'text' => $text,
            'source_language' => $from,
            'target_language' => $to,
        ]);
    }

    /**
     * Convert text to speech.
     */
    public function textToSpeech(string $text, string $language = 'th', string $voice = 'default'): mixed
    {
        return $this->execute('POST', '/tts', [
            'text' => $text,
            'language' => $language,
            'voice' => $voice,
        ]);
    }

    /**
     * Handle AI call center interaction.
     */
    public function callCenter(string $action, array $params = []): mixed
    {
        return $this->execute('POST', '/call-center/' . $action, $params);
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
