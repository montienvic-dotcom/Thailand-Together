<?php

namespace App\Services\ApiGateway\Adapters;

/**
 * HelpDesk adapter for customer support and ticket management.
 * Supports ticket creation, SLA tracking, and escalation.
 */
class HelpDeskAdapter extends BaseAdapter
{
    public function providerName(): string
    {
        return $this->config['provider_name'] ?? 'HelpDesk';
    }

    /**
     * Create a support ticket.
     */
    public function createTicket(array $params): mixed
    {
        return $this->execute('POST', '/tickets', $params);
    }

    /**
     * Get ticket details.
     */
    public function getTicket(string $ticketId): mixed
    {
        return $this->execute('GET', "/tickets/{$ticketId}");
    }

    /**
     * Update a ticket (status, priority, assignment).
     */
    public function updateTicket(string $ticketId, array $params): mixed
    {
        return $this->execute('PUT', "/tickets/{$ticketId}", $params);
    }

    /**
     * Add a comment/reply to a ticket.
     */
    public function addComment(string $ticketId, string $body, bool $isPublic = true): mixed
    {
        return $this->execute('POST', "/tickets/{$ticketId}/comments", [
            'body' => $body,
            'is_public' => $isPublic,
        ]);
    }

    /**
     * List tickets with filters.
     */
    public function listTickets(array $filters = []): mixed
    {
        return $this->execute('GET', '/tickets', $filters);
    }

    /**
     * Escalate a ticket.
     */
    public function escalate(string $ticketId, string $reason): mixed
    {
        return $this->execute('POST', "/tickets/{$ticketId}/escalate", [
            'reason' => $reason,
        ]);
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
