<?php

namespace App\Services\ApiGateway\Adapters;

/**
 * Generic payment gateway adapter.
 * Subclass this for specific providers (Stripe, PromptPay, VNPay).
 */
class PaymentAdapter extends BaseAdapter
{
    public function providerName(): string
    {
        return $this->config['provider_name'] ?? 'Payment Gateway';
    }

    /**
     * Create a payment intent/charge.
     */
    public function createPayment(array $params): mixed
    {
        return $this->execute('POST', '/payments', $params);
    }

    /**
     * Check payment status.
     */
    public function getPaymentStatus(string $paymentId): mixed
    {
        return $this->execute('GET', "/payments/{$paymentId}");
    }

    /**
     * Process a refund.
     */
    public function refund(string $paymentId, ?float $amount = null): mixed
    {
        return $this->execute('POST', "/payments/{$paymentId}/refund", array_filter([
            'amount' => $amount,
        ]));
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
