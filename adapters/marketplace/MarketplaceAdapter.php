<?php

declare(strict_types=1);

namespace ThailandTogether\Adapters\Marketplace;

use ThailandTogether\Adapters\BaseAdapter;

/**
 * Adapter for the Marketplace external system.
 *
 * Provides product catalog, ordering, and search operations.
 * Currently returns simulated data; will connect to a real system later.
 */
class MarketplaceAdapter extends BaseAdapter
{
    protected string $name = 'marketplace';
    protected string $version = '1.0.0';

    private const SUPPORTED_ACTIONS = [
        'listProducts',
        'getProduct',
        'createOrder',
        'getOrderStatus',
        'searchProducts',
        'getCategories',
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
            'message' => 'Marketplace system is operational (simulated).',
            'details' => [
                'endpoint'   => $this->config['base_url'] ?? 'https://marketplace.example.com',
                'latency_ms' => 35,
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
    protected function actionListProducts(array $params): array
    {
        $products = $this->sampleProducts();

        if (isset($params['category'])) {
            $products = array_values(
                array_filter($products, fn($p) => $p['category'] === $params['category'])
            );
        }

        $perPage = $params['per_page'] ?? 10;
        $page    = $params['page'] ?? 1;

        return [
            'products' => array_slice($products, ($page - 1) * $perPage, $perPage),
            'total'    => count($products),
            'page'     => $page,
            'per_page' => $perPage,
        ];
    }

    /**
     * @param array{product_id: string} $params
     */
    protected function actionGetProduct(array $params): array
    {
        $productId = $params['product_id'] ?? 'PROD-001';
        $products  = $this->sampleProducts();
        $product   = collect($products)->firstWhere('id', $productId);

        if ($product === null) {
            return ['error' => 'Product not found', 'product_id' => $productId];
        }

        $product['reviews'] = [
            ['user' => 'Alice T.', 'rating' => 5, 'comment' => 'Excellent quality!'],
            ['user' => 'Bob K.',   'rating' => 4, 'comment' => 'Good value for money.'],
            ['user' => 'Carla S.', 'rating' => 5, 'comment' => 'Love it, very authentic.'],
        ];

        $product['related_products'] = ['PROD-002', 'PROD-004'];

        return $product;
    }

    /**
     * @param array{items: array<array{product_id: string, quantity: int}>, customer_name: string, shipping_address?: string} $params
     */
    protected function actionCreateOrder(array $params): array
    {
        $orderId = 'ORD-' . strtoupper(substr(md5((string) microtime(true)), 0, 8));
        $items   = $params['items'] ?? [['product_id' => 'PROD-001', 'quantity' => 1]];

        $totalAmount = 0.0;
        $orderItems  = [];

        foreach ($items as $item) {
            $unitPrice = rand(150, 3500) * 1.0;
            $lineTotal = $unitPrice * ($item['quantity'] ?? 1);
            $totalAmount += $lineTotal;

            $orderItems[] = [
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'] ?? 1,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
            ];
        }

        return [
            'order_id'         => $orderId,
            'status'           => 'pending',
            'customer_name'    => $params['customer_name'] ?? 'Guest',
            'items'            => $orderItems,
            'total'            => ['amount' => $totalAmount, 'currency' => 'THB'],
            'shipping_address' => $params['shipping_address'] ?? 'Hotel delivery - Pattaya',
            'estimated_delivery' => now()->addDays(3)->toDateString(),
            'created_at'       => now()->toIso8601String(),
        ];
    }

    /**
     * @param array{order_id: string} $params
     */
    protected function actionGetOrderStatus(array $params): array
    {
        $statuses = ['pending', 'processing', 'shipped', 'delivered'];
        $status   = $statuses[array_rand($statuses)];

        return [
            'order_id'   => $params['order_id'] ?? 'ORD-UNKNOWN',
            'status'     => $status,
            'updated_at' => now()->toIso8601String(),
            'timeline'   => [
                ['status' => 'pending',    'at' => now()->subHours(48)->toIso8601String()],
                ['status' => 'processing', 'at' => now()->subHours(24)->toIso8601String()],
                ['status' => 'shipped',    'at' => now()->subHours(6)->toIso8601String()],
            ],
            'tracking' => [
                'carrier' => 'Kerry Express',
                'number'  => 'KRY' . rand(100000000, 999999999),
            ],
        ];
    }

    /**
     * @param array{query: string, category?: string, min_price?: float, max_price?: float} $params
     */
    protected function actionSearchProducts(array $params): array
    {
        $query    = strtolower($params['query'] ?? '');
        $products = $this->sampleProducts();

        $results = array_values(array_filter($products, function ($p) use ($query, $params) {
            $matchesQuery = $query === ''
                || str_contains(strtolower($p['name']), $query)
                || str_contains(strtolower($p['description']), $query);

            $matchesCategory = !isset($params['category'])
                || $p['category'] === $params['category'];

            $matchesPrice = true;
            if (isset($params['min_price']) && $p['price']['amount'] < $params['min_price']) {
                $matchesPrice = false;
            }
            if (isset($params['max_price']) && $p['price']['amount'] > $params['max_price']) {
                $matchesPrice = false;
            }

            return $matchesQuery && $matchesCategory && $matchesPrice;
        }));

        return [
            'query'   => $params['query'] ?? '',
            'results' => $results,
            'total'   => count($results),
        ];
    }

    protected function actionGetCategories(array $params): array
    {
        return [
            'categories' => [
                ['code' => 'souvenirs',   'name' => 'Souvenirs & Gifts',  'product_count' => 156],
                ['code' => 'fashion',     'name' => 'Thai Fashion',       'product_count' => 89],
                ['code' => 'food',        'name' => 'Food & Snacks',      'product_count' => 234],
                ['code' => 'handicrafts', 'name' => 'Thai Handicrafts',   'product_count' => 67],
                ['code' => 'health',      'name' => 'Health & Wellness',  'product_count' => 112],
                ['code' => 'electronics', 'name' => 'Electronics',        'product_count' => 45],
            ],
        ];
    }

    // ── Sample data ──────────────────────────────────────────────────

    private function sampleProducts(): array
    {
        return [
            [
                'id'          => 'PROD-001',
                'name'        => 'Thai Silk Scarf',
                'category'    => 'fashion',
                'price'       => ['amount' => 890.00, 'currency' => 'THB'],
                'stock'       => 45,
                'merchant'    => 'Silk Paradise Pattaya',
                'rating'      => 4.8,
                'description' => 'Handwoven Thai silk scarf with traditional patterns.',
            ],
            [
                'id'          => 'PROD-002',
                'name'        => 'Elephant Wood Carving',
                'category'    => 'handicrafts',
                'price'       => ['amount' => 1500.00, 'currency' => 'THB'],
                'stock'       => 18,
                'merchant'    => 'Artisan Village',
                'rating'      => 4.6,
                'description' => 'Hand-carved teak wood elephant figurine by local artisans.',
            ],
            [
                'id'          => 'PROD-003',
                'name'        => 'Tom Yum Spice Set',
                'category'    => 'food',
                'price'       => ['amount' => 350.00, 'currency' => 'THB'],
                'stock'       => 200,
                'merchant'    => 'Thai Taste Co.',
                'rating'      => 4.9,
                'description' => 'Authentic Tom Yum spice set with lemongrass, galangal, and kaffir lime.',
            ],
            [
                'id'          => 'PROD-004',
                'name'        => 'Coconut Oil Gift Set',
                'category'    => 'health',
                'price'       => ['amount' => 650.00, 'currency' => 'THB'],
                'stock'       => 75,
                'merchant'    => 'Coco Natural',
                'rating'      => 4.5,
                'description' => 'Cold-pressed virgin coconut oil set with massage oil and lip balm.',
            ],
            [
                'id'          => 'PROD-005',
                'name'        => 'Pattaya Beach Keychain Set',
                'category'    => 'souvenirs',
                'price'       => ['amount' => 150.00, 'currency' => 'THB'],
                'stock'       => 500,
                'merchant'    => 'Beach Memories Shop',
                'rating'      => 4.2,
                'description' => 'Set of 3 keychains featuring iconic Pattaya beach scenes.',
            ],
            [
                'id'          => 'PROD-006',
                'name'        => 'Thai Boxing Shorts',
                'category'    => 'fashion',
                'price'       => ['amount' => 450.00, 'currency' => 'THB'],
                'stock'       => 60,
                'merchant'    => 'Muay Thai Gear',
                'rating'      => 4.7,
                'description' => 'Authentic Muay Thai boxing shorts with embroidered design.',
            ],
        ];
    }
}
