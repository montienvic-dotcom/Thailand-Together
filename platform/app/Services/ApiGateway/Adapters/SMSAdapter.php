<?php

namespace App\Services\ApiGateway\Adapters;

/**
 * SMS service adapter.
 * Supports Twilio, local SMS providers per country.
 */
class SMSAdapter extends BaseAdapter
{
    public function providerName(): string
    {
        return $this->config['provider_name'] ?? 'SMS Service';
    }

    /**
     * Send an SMS message.
     */
    public function send(string $to, string $message, array $options = []): mixed
    {
        return $this->execute('POST', '/messages', array_merge([
            'to' => $to,
            'body' => $message,
        ], $options));
    }

    /**
     * Send OTP for verification.
     */
    public function sendOtp(string $to, string $code): mixed
    {
        return $this->send($to, "Your verification code is: {$code}");
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
