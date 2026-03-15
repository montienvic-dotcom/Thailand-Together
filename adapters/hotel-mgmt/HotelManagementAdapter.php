<?php

declare(strict_types=1);

namespace ThailandTogether\Adapters\HotelMgmt;

use ThailandTogether\Adapters\BaseAdapter;

/**
 * Adapter for the Hotel Management external system.
 *
 * Provides room inventory, availability, and reservation operations.
 * Currently returns simulated data; will connect to a real PMS later.
 */
class HotelManagementAdapter extends BaseAdapter
{
    protected string $name = 'hotel-mgmt';
    protected string $version = '1.0.0';

    private const SUPPORTED_ACTIONS = [
        'listRooms',
        'checkAvailability',
        'createReservation',
        'cancelReservation',
        'getRoomTypes',
        'getOccupancyRate',
    ];

    public function getSupportedActions(): array
    {
        return self::SUPPORTED_ACTIONS;
    }

    public function isAvailable(): bool
    {
        // In production this would ping the PMS endpoint.
        return true;
    }

    public function healthCheck(): array
    {
        return [
            'status'  => 'ok',
            'message' => 'Hotel Management system is operational (simulated).',
            'details' => [
                'endpoint'   => $this->config['base_url'] ?? 'https://pms.example.com',
                'latency_ms' => 42,
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
     * @param array{hotel_id?: string, floor?: int} $params
     */
    protected function actionListRooms(array $params): array
    {
        $hotelId = $params['hotel_id'] ?? 'HTL-PTY-001';

        return [
            'hotel_id' => $hotelId,
            'rooms'    => [
                [
                    'id'     => 'RM-101',
                    'number' => '101',
                    'type'   => 'deluxe',
                    'floor'  => 1,
                    'status' => 'available',
                    'price'  => ['amount' => 3500.00, 'currency' => 'THB'],
                ],
                [
                    'id'     => 'RM-102',
                    'number' => '102',
                    'type'   => 'deluxe',
                    'floor'  => 1,
                    'status' => 'occupied',
                    'price'  => ['amount' => 3500.00, 'currency' => 'THB'],
                ],
                [
                    'id'     => 'RM-201',
                    'number' => '201',
                    'type'   => 'suite',
                    'floor'  => 2,
                    'status' => 'available',
                    'price'  => ['amount' => 7800.00, 'currency' => 'THB'],
                ],
                [
                    'id'     => 'RM-301',
                    'number' => '301',
                    'type'   => 'presidential',
                    'floor'  => 3,
                    'status' => 'available',
                    'price'  => ['amount' => 15000.00, 'currency' => 'THB'],
                ],
                [
                    'id'     => 'RM-103',
                    'number' => '103',
                    'type'   => 'standard',
                    'floor'  => 1,
                    'status' => 'maintenance',
                    'price'  => ['amount' => 1800.00, 'currency' => 'THB'],
                ],
            ],
        ];
    }

    /**
     * @param array{room_type?: string, check_in: string, check_out: string} $params
     */
    protected function actionCheckAvailability(array $params): array
    {
        $checkIn  = $params['check_in'] ?? now()->toDateString();
        $checkOut = $params['check_out'] ?? now()->addDays(2)->toDateString();
        $roomType = $params['room_type'] ?? null;

        $rooms = [
            ['id' => 'RM-101', 'type' => 'deluxe', 'price_per_night' => 3500.00],
            ['id' => 'RM-201', 'type' => 'suite', 'price_per_night' => 7800.00],
            ['id' => 'RM-301', 'type' => 'presidential', 'price_per_night' => 15000.00],
        ];

        if ($roomType !== null) {
            $rooms = array_values(array_filter($rooms, fn($r) => $r['type'] === $roomType));
        }

        return [
            'check_in'        => $checkIn,
            'check_out'       => $checkOut,
            'available_rooms' => $rooms,
            'total_available'  => count($rooms),
        ];
    }

    /**
     * @param array{guest_name: string, room_id: string, check_in: string, check_out: string, guests?: int} $params
     */
    protected function actionCreateReservation(array $params): array
    {
        $reservationId = 'RSV-' . strtoupper(substr(md5((string) microtime(true)), 0, 8));

        return [
            'reservation_id' => $reservationId,
            'status'         => 'confirmed',
            'guest_name'     => $params['guest_name'] ?? 'Guest',
            'room_id'        => $params['room_id'] ?? 'RM-101',
            'check_in'       => $params['check_in'] ?? now()->toDateString(),
            'check_out'      => $params['check_out'] ?? now()->addDays(2)->toDateString(),
            'guests'         => $params['guests'] ?? 1,
            'total_price'    => ['amount' => 7000.00, 'currency' => 'THB'],
            'created_at'     => now()->toIso8601String(),
        ];
    }

    /**
     * @param array{reservation_id: string, reason?: string} $params
     */
    protected function actionCancelReservation(array $params): array
    {
        return [
            'reservation_id' => $params['reservation_id'] ?? 'RSV-UNKNOWN',
            'status'         => 'cancelled',
            'reason'         => $params['reason'] ?? 'Guest requested cancellation',
            'refund'         => ['amount' => 7000.00, 'currency' => 'THB'],
            'cancelled_at'   => now()->toIso8601String(),
        ];
    }

    protected function actionGetRoomTypes(array $params): array
    {
        return [
            'room_types' => [
                [
                    'code'          => 'standard',
                    'name'          => 'Standard Room',
                    'max_guests'    => 2,
                    'base_price'    => 1800.00,
                    'amenities'     => ['Wi-Fi', 'TV', 'Air Conditioning', 'Mini Bar'],
                ],
                [
                    'code'          => 'deluxe',
                    'name'          => 'Deluxe Room',
                    'max_guests'    => 2,
                    'base_price'    => 3500.00,
                    'amenities'     => ['Wi-Fi', 'TV', 'Air Conditioning', 'Mini Bar', 'Sea View', 'Balcony'],
                ],
                [
                    'code'          => 'suite',
                    'name'          => 'Executive Suite',
                    'max_guests'    => 4,
                    'base_price'    => 7800.00,
                    'amenities'     => ['Wi-Fi', 'TV', 'Air Conditioning', 'Mini Bar', 'Sea View', 'Balcony', 'Living Area', 'Jacuzzi'],
                ],
                [
                    'code'          => 'presidential',
                    'name'          => 'Presidential Suite',
                    'max_guests'    => 6,
                    'base_price'    => 15000.00,
                    'amenities'     => ['Wi-Fi', 'TV', 'Air Conditioning', 'Mini Bar', 'Panoramic Sea View', 'Private Pool', 'Butler Service', 'Kitchen'],
                ],
            ],
        ];
    }

    /**
     * @param array{hotel_id?: string, date?: string} $params
     */
    protected function actionGetOccupancyRate(array $params): array
    {
        return [
            'hotel_id'       => $params['hotel_id'] ?? 'HTL-PTY-001',
            'date'           => $params['date'] ?? now()->toDateString(),
            'total_rooms'    => 120,
            'occupied'       => 87,
            'occupancy_rate' => 72.5,
            'by_type'        => [
                'standard'      => ['total' => 40, 'occupied' => 35, 'rate' => 87.5],
                'deluxe'        => ['total' => 40, 'occupied' => 30, 'rate' => 75.0],
                'suite'         => ['total' => 30, 'occupied' => 18, 'rate' => 60.0],
                'presidential'  => ['total' => 10, 'occupied' => 4,  'rate' => 40.0],
            ],
        ];
    }
}
