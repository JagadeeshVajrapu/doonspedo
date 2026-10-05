<?php

namespace App\Support;

use App\Models\VehicleCategory;

class RideFare
{
    /**
     * Authoritative fare. Mirrors the rider app total:
     * base + distance × (category rate + AC/non-AC where it applies),
     * then parcel weight, the existing WELCOME50 coupon, rain surge, and night premium.
     */
    public static function calculate(VehicleCategory $category, float $distance, array $context = []): float
    {
        $settings = $context['settings'] ?? load_sys_settings();
        $rate = (float) $category->rate_per_km;
        $preference = self::normalizePreference($context['ac_preference'] ?? null);

        if ($preference && !self::isBikeOrAuto($category->name)) {
            $extraKey = $preference === 'ac' ? 'ac_rate_per_km' : 'non_ac_rate_per_km';
            $extra = (float) ($settings[$extraKey] ?? 0);
            if ($extra > 0) {
                $rate += $extra;
            }
        }

        $fare = (float) $category->base_fare + ($distance * $rate);

        $weight = (float) ($context['parcel_weight'] ?? 0);
        if (($context['service_type'] ?? '') === 'parcel' && $weight > 0) {
            $fare += $weight * 10;
        }

        if (strtoupper(trim((string) ($context['coupon_code'] ?? ''))) === 'WELCOME50') {
            $fare -= 50;
        }

        $fare = max(0, $fare);

        if ((string) ($settings['rain_surge_enabled'] ?? '0') === '1') {
            $multiplier = (float) ($settings['rain_surge_multiplier'] ?? 1);
            if ($multiplier > 10) {
                $multiplier = 1 + ($multiplier / 100);
            }
            $fare *= $multiplier;
        }

        if ((string) ($settings['night_premium_enabled'] ?? '0') === '1') {
            $hour = array_key_exists('hour', $context) ? (int) $context['hour'] : (int) now()->format('G');
            if ($hour >= 0 && $hour < 5) {
                $fare += (float) ($settings['night_premium_amount'] ?? 0);
            }
        }

        return round($fare, 2);
    }

    public static function normalizePreference(?string $value): ?string
    {
        $value = strtolower(trim((string) $value));
        if ($value === 'ac' || $value === 'non-ac') {
            return $value;
        }
        if ($value === 'non ac' || $value === 'nonac') {
            return 'non-ac';
        }

        return null;
    }

    public static function preferenceFromNotes(?string $notes): ?string
    {
        if (preg_match('/Preference:\s*(NON-AC|AC)\b/i', (string) $notes, $match)) {
            return strcasecmp($match[1], 'NON-AC') === 0 ? 'non-ac' : 'ac';
        }

        return null;
    }

    public static function isBikeOrAuto(?string $name): bool
    {
        $name = strtolower((string) $name);

        return str_contains($name, 'bike')
            || str_contains($name, 'auto')
            || str_contains($name, 'cycle')
            || str_contains($name, 'moto');
    }
}
