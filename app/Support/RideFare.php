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
        $localRate = self::dehradunRate($category, $distance, $context, $settings);
        if ($localRate !== null) {
            $rate = $localRate;
        }
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

    public static function dehradunRates(array $settings = []): array
    {
        $bike = $settings['dehradun_bike_rate_per_km'] ?? 8;
        $auto = $settings['dehradun_auto_rate_per_km'] ?? 12;
        $car = $settings['dehradun_car_rate_per_km'] ?? 20;
        $max = $settings['dehradun_max_local_km'] ?? 40;

        return [
            'bike' => max(0, (float) $bike),
            'auto' => max(0, (float) $auto),
            'car' => max(0, (float) $car),
            'max_km' => max(0.1, (float) $max),
        ];
    }

    public static function isDehradunPickup(?string $pickup, $lat = null, $lng = null): bool
    {
        $inside = self::pointInDehradun($lat, $lng);
        if ($inside === true) {
            return true;
        }
        if ($inside === false) {
            return false;
        }

        return (bool) preg_match('/\bdehradun\b/i', (string) $pickup);
    }

    /**
     * Local Dehradun rides use the admin per-km rate. Trips past the configured
     * distance are refused by the booking controller and are not given another rate.
     */
    public static function dehradunLimitMessage(VehicleCategory $category, float $distance, array $context = []): ?string
    {
        unset($category);
        $settings = $context['settings'] ?? load_sys_settings();
        if (($context['service_type'] ?? 'ride') !== 'ride' || !empty($context['is_rental'])) {
            return null;
        }
        if (!self::isDehradunPickup($context['pickup_location'] ?? null, $context['pickup_lat'] ?? null, $context['pickup_lng'] ?? null)) {
            return null;
        }

        $measured = self::measuredDistance($distance, $context);
        $max = self::dehradunRates($settings)['max_km'];
        if ($measured <= $max + 0.000001) {
            return null;
        }

        return 'This trip is '.number_format($measured, 1).' km, beyond the '.number_format($max, 0).' km Dehradun local limit. Local rates cannot be used for this trip.';
    }

    public static function measuredDistance(float $distance, array $context): float
    {
        $distance = max(0, $distance);
        $pickupLat = $context['pickup_lat'] ?? null;
        $pickupLng = $context['pickup_lng'] ?? null;
        $dropLat = $context['dropoff_lat'] ?? null;
        $dropLng = $context['dropoff_lng'] ?? null;
        if (!is_numeric($pickupLat) || !is_numeric($pickupLng) || !is_numeric($dropLat) || !is_numeric($dropLng)) {
            return round($distance, 2);
        }

        $straight = Geo::kilometers((float) $pickupLat, (float) $pickupLng, (float) $dropLat, (float) $dropLng);

        return round(max($distance, $straight), 2);
    }

    private static function dehradunRate(VehicleCategory $category, float $distance, array $context, array $settings): ?float
    {
        if (self::dehradunLimitMessage($category, $distance, $context + ['settings' => $settings])) {
            return null;
        }
        if (($context['service_type'] ?? 'ride') !== 'ride' || !empty($context['is_rental'])) {
            return null;
        }
        if (!self::isDehradunPickup($context['pickup_location'] ?? null, $context['pickup_lat'] ?? null, $context['pickup_lng'] ?? null)) {
            return null;
        }

        $class = self::dehradunVehicleClass($category->name);
        if ($class === null) {
            return null;
        }

        return self::dehradunRates($settings)[$class];
    }

    public static function dehradunVehicleClass(?string $name): ?string
    {
        $name = strtolower((string) $name);
        if (str_contains($name, 'bike') || str_contains($name, 'cycle') || str_contains($name, 'moto')) {
            return 'bike';
        }
        if (str_contains($name, 'auto')) {
            return 'auto';
        }
        if (str_contains($name, 'cab') || str_contains($name, 'car') || str_contains($name, 'suv') || str_contains($name, 'sedan') || str_contains($name, 'taxi')) {
            return 'car';
        }

        return null;
    }

    private static function pointInDehradun($lat, $lng): ?bool
    {
        if (!is_numeric($lat) || !is_numeric($lng)) {
            return null;
        }

        $lat = (float) $lat;
        $lng = (float) $lng;

        $inside = $lat >= 30.24 && $lat <= 30.42 && $lng >= 77.93 && $lng <= 78.18;

        return $inside;
    }

    /**
     * GST is not charged. The amount previously shown as 18% GST stays inside
     * the base fare. The existing 2% service fee stays separate. The total is
     * the server fare and is not increased.
     */
    public static function breakdown(float $total): array
    {
        $total = round(max(0, $total), 2);
        $serviceFee = round($total * 0.02, 2);
        $baseFare = round($total - $serviceFee, 2);

        return [
            'base_fare' => $baseFare,
            'tax' => 0.0,
            'service_fee' => $serviceFee,
            'total' => $total,
        ];
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
