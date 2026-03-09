<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds merchant_i18n table with multi-language names for all merchants.
 * Languages: th, en, zh, ja, ko, ru
 */
class MerchantI18nSeeder extends Seeder
{
    public function run(): void
    {
        $merchants = DB::table('merchant')->get();

        if ($merchants->isEmpty()) {
            $this->command->warn('No merchants found. Run MerchantDemoSeeder first.');
            return;
        }

        $categoryNames = [
            'restaurant' => ['zh' => '餐厅', 'ja' => 'レストラン', 'ko' => '레스토랑', 'ru' => 'Ресторан'],
            'cafe'       => ['zh' => '咖啡馆', 'ja' => 'カフェ', 'ko' => '카페', 'ru' => 'Кафе'],
            'market'     => ['zh' => '商店', 'ja' => 'ショップ', 'ko' => '상점', 'ru' => 'Магазин'],
            'mall'       => ['zh' => '商场店', 'ja' => 'モールショップ', 'ko' => '몰 상점', 'ru' => 'Торговый центр'],
            'hotel'      => ['zh' => '酒店', 'ja' => 'ホテル', 'ko' => '호텔', 'ru' => 'Отель'],
            'spa'        => ['zh' => '水疗', 'ja' => 'スパ', 'ko' => '스파', 'ru' => 'Спа'],
            'show'       => ['zh' => '表演', 'ja' => 'ショー', 'ko' => '쇼', 'ru' => 'Шоу'],
            'entertainment' => ['zh' => '娱乐', 'ja' => 'エンタメ', 'ko' => '엔터테인먼트', 'ru' => 'Развлечения'],
            'wellness'   => ['zh' => '健康', 'ja' => 'ウェルネス', 'ko' => '웰니스', 'ru' => 'Велнес'],
            'transport'  => ['zh' => '交通', 'ja' => '交通', 'ko' => '교통', 'ru' => 'Транспорт'],
            'tour'       => ['zh' => '旅游', 'ja' => 'ツアー', 'ko' => '투어', 'ru' => 'Тур'],
            'attraction' => ['zh' => '景点', 'ja' => 'アトラクション', 'ko' => '관광지', 'ru' => 'Достопримечательность'],
            'beach'      => ['zh' => '海滩', 'ja' => 'ビーチ', 'ko' => '해변', 'ru' => 'Пляж'],
            'office'     => ['zh' => '办公室', 'ja' => 'オフィス', 'ko' => '오피스', 'ru' => 'Офис'],
            'venue'      => ['zh' => '场馆', 'ja' => '会場', 'ko' => '행사장', 'ru' => 'Площадка'],
            'service'    => ['zh' => '服务', 'ja' => 'サービス', 'ko' => '서비스', 'ru' => 'Сервис'],
        ];

        $languages = ['th', 'en', 'zh', 'ja', 'ko', 'ru'];

        foreach ($merchants as $merchant) {
            $mId = $merchant->merchant_id ?? $merchant->id;
            $category = $merchant->category ?? 'service';

            foreach ($languages as $lang) {
                $name = match ($lang) {
                    'th' => $merchant->merchant_name_th,
                    'en' => $merchant->merchant_name_en,
                    default => ($categoryNames[$category][$lang] ?? $categoryNames['service'][$lang])
                        . ' ' . $merchant->merchant_code,
                };

                DB::table('merchant_i18n')->updateOrInsert(
                    ['merchant_id' => $mId, 'lang' => $lang],
                    ['name' => $name, 'description' => null, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        $this->command->info('Merchant i18n seeded for ' . $merchants->count() . ' merchants × ' . count($languages) . ' languages');
    }
}
