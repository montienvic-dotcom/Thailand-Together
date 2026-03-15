<?php

declare(strict_types=1);

namespace ThailandTogether\Adapters\TourBooking;

use ThailandTogether\Adapters\BaseAdapter;

/**
 * Adapter for the Tour Booking external system.
 *
 * Provides tour catalog, availability, and booking operations.
 * Currently returns simulated data; will connect to a real system later.
 */
class TourBookingAdapter extends BaseAdapter
{
    protected string $name = 'tour-booking';
    protected string $version = '1.0.0';

    private const SUPPORTED_ACTIONS = [
        'listTours',
        'getTourDetail',
        'checkAvailability',
        'createBooking',
        'cancelBooking',
        'getUpcomingTours',
    ];

    public function getSupportedActions(): array
    {
        return self::SUPPORTED_ACTIONS;
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function healthCheck(): array
    {
        return [
            'status'  => 'ok',
            'message' => 'Tour Booking system is operational (simulated).',
            'details' => [
                'endpoint'   => $this->config['base_url'] ?? 'https://tours.example.com',
                'latency_ms' => 38,
            ],
        ];
    }

    public function execute(string $action, array $params = []): mixed
    {
        $this->log('info', "Executing action [{$action}]", ['params' => $params]);
        return $this->dispatch($action, $params);
    }

    // ── Actions ──────────────────────────────────────────────────────

    /**
     * @param array{category?: string, page?: int, per_page?: int} $params
     */
    protected function actionListTours(array $params): array
    {
        $tours = $this->sampleTours();

        if (isset($params['category'])) {
            $tours = array_values(
                array_filter($tours, fn($t) => $t['category'] === $params['category'])
            );
        }

        return [
            'tours' => $tours,
            'total' => count($tours),
            'page'  => $params['page'] ?? 1,
        ];
    }

    /**
     * @param array{tour_id: string} $params
     */
    protected function actionGetTourDetail(array $params): array
    {
        $tourId = $params['tour_id'] ?? 'TOUR-001';
        $tours  = $this->sampleTours();
        $tour   = collect($tours)->firstWhere('id', $tourId);

        if ($tour === null) {
            return ['error' => 'Tour not found', 'tour_id' => $tourId];
        }

        $tour['itinerary'] = [
            ['time' => '08:00', 'activity' => 'Hotel pickup'],
            ['time' => '09:00', 'activity' => 'Arrive at first location'],
            ['time' => '12:00', 'activity' => 'Lunch break (included)'],
            ['time' => '13:30', 'activity' => 'Continue sightseeing'],
            ['time' => '16:00', 'activity' => 'Free time for shopping'],
            ['time' => '17:30', 'activity' => 'Return to hotel'],
        ];

        $tour['reviews'] = [
            ['user' => 'John D.', 'rating' => 5, 'comment' => 'Amazing experience!'],
            ['user' => 'Sarah M.', 'rating' => 4, 'comment' => 'Great tour, guide was very knowledgeable.'],
        ];

        return $tour;
    }

    /**
     * @param array{tour_id: string, date: string, guests?: int} $params
     */
    protected function actionCheckAvailability(array $params): array
    {
        $maxSlots = rand(5, 20);
        $booked   = rand(0, $maxSlots - 1);

        return [
            'tour_id'          => $params['tour_id'] ?? 'TOUR-001',
            'date'             => $params['date'] ?? now()->addDays(3)->toDateString(),
            'total_slots'      => $maxSlots,
            'booked_slots'     => $booked,
            'available_slots'  => $maxSlots - $booked,
            'is_available'     => ($maxSlots - $booked) >= ($params['guests'] ?? 1),
        ];
    }

    /**
     * @param array{tour_id: string, date: string, guest_name: string, guests?: int, special_requests?: string} $params
     */
    protected function actionCreateBooking(array $params): array
    {
        $bookingId = 'TBK-' . strtoupper(substr(md5((string) microtime(true)), 0, 8));
        $guests    = $params['guests'] ?? 1;

        return [
            'booking_id'       => $bookingId,
            'status'           => 'confirmed',
            'tour_id'          => $params['tour_id'] ?? 'TOUR-001',
            'date'             => $params['date'] ?? now()->addDays(3)->toDateString(),
            'guest_name'       => $params['guest_name'] ?? 'Guest',
            'guests'           => $guests,
            'special_requests' => $params['special_requests'] ?? null,
            'total_price'      => ['amount' => 2500.00 * $guests, 'currency' => 'THB'],
            'pickup_time'      => '08:00',
            'pickup_location'  => 'Hotel lobby',
            'created_at'       => now()->toIso8601String(),
        ];
    }

    /**
     * @param array{booking_id: string, reason?: string} $params
     */
    protected function actionCancelBooking(array $params): array
    {
        return [
            'booking_id'    => $params['booking_id'] ?? 'TBK-UNKNOWN',
            'status'        => 'cancelled',
            'reason'        => $params['reason'] ?? 'Guest requested cancellation',
            'refund'        => ['amount' => 2500.00, 'currency' => 'THB'],
            'refund_policy' => 'Full refund for cancellations 24+ hours in advance.',
            'cancelled_at'  => now()->toIso8601String(),
        ];
    }

    /**
     * @param array{days_ahead?: int, limit?: int} $params
     */
    protected function actionGetUpcomingTours(array $params): array
    {
        $daysAhead = $params['days_ahead'] ?? 7;
        $limit     = $params['limit'] ?? 5;

        $upcoming = [];
        $tours    = $this->sampleTours();

        for ($i = 1; $i <= min($limit, count($tours)); $i++) {
            $tour = $tours[$i - 1];
            $tour['next_date']       = now()->addDays(rand(1, $daysAhead))->toDateString();
            $tour['available_slots'] = rand(2, 15);
            $upcoming[]              = $tour;
        }

        return [
            'upcoming_tours' => $upcoming,
            'period'         => "{$daysAhead} days",
        ];
    }

    // ── Sample data ──────────────────────────────────────────────────

    private function sampleTours(): array
    {
        return [
            [
                'id'          => 'TOUR-001',
                'name'        => 'Coral Island Day Trip',
                'category'    => 'island',
                'duration'    => '8 hours',
                'price'       => ['amount' => 2500.00, 'currency' => 'THB'],
                'rating'      => 4.7,
                'max_guests'  => 20,
                'includes'    => ['Speedboat transfer', 'Lunch', 'Snorkeling gear', 'Guide'],
                'description' => 'Visit Koh Larn (Coral Island) with snorkeling, swimming, and a beachside lunch.',
            ],
            [
                'id'          => 'TOUR-002',
                'name'        => 'Pattaya City Night Tour',
                'category'    => 'city',
                'duration'    => '4 hours',
                'price'       => ['amount' => 1200.00, 'currency' => 'THB'],
                'rating'      => 4.3,
                'max_guests'  => 30,
                'includes'    => ['Air-conditioned van', 'Guide', 'Walking Street visit'],
                'description' => 'Explore Pattaya nightlife, viewpoints, and famous Walking Street.',
            ],
            [
                'id'          => 'TOUR-003',
                'name'        => 'Nong Nooch Tropical Garden',
                'category'    => 'nature',
                'duration'    => '6 hours',
                'price'       => ['amount' => 1800.00, 'currency' => 'THB'],
                'rating'      => 4.5,
                'max_guests'  => 25,
                'includes'    => ['Entry ticket', 'Thai cultural show', 'Elephant show', 'Lunch'],
                'description' => 'Award-winning tropical garden with cultural shows and stunning landscaping.',
            ],
            [
                'id'          => 'TOUR-004',
                'name'        => 'Sanctuary of Truth',
                'category'    => 'culture',
                'duration'    => '3 hours',
                'price'       => ['amount' => 1500.00, 'currency' => 'THB'],
                'rating'      => 4.8,
                'max_guests'  => 15,
                'includes'    => ['Entry ticket', 'Guide', 'Boat ride', 'Cultural demonstration'],
                'description' => 'Visit the magnificent all-wood temple showcasing ancient Thai art and philosophy.',
            ],
            [
                'id'          => 'TOUR-005',
                'name'        => 'Four Islands Snorkeling Adventure',
                'category'    => 'island',
                'duration'    => '10 hours',
                'price'       => ['amount' => 3800.00, 'currency' => 'THB'],
                'rating'      => 4.6,
                'max_guests'  => 12,
                'includes'    => ['Speedboat', 'Snorkeling gear', 'Lunch', 'Drinks', 'Professional guide'],
                'description' => 'Full-day snorkeling adventure visiting four stunning islands near Pattaya.',
            ],
        ];
    }
}
