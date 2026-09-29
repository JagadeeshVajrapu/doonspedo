<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\DriverRegistration;

class Geo
{
    public const DEFAULT_RADIUS_KM = 10.0;

    public const MAX_RADIUS_KM = 15.0;

    public static function kilometers(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earth = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earth * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public static function etaMinutes(float $kilometers): int
    {
        $roadKm = $kilometers * 1.3;

        return max(1, (int) round($roadKm * 2.5));
    }

    public static function nearbyRadiusKm(DriverRegistration $driver): float
    {
        $preference = $driver->ride_preferences['max_distance'] ?? null;
        $km = is_numeric($preference) ? (float) $preference : self::DEFAULT_RADIUS_KM;

        return max(1.0, min(self::MAX_RADIUS_KM, $km));
    }

    public static function outOfRangeMessage(?DriverRegistration $driver, Booking $booking): ?string
    {
        if (!$driver || $driver->current_lat === null || $driver->current_lng === null) {
            return 'Turn on location so nearby ride requests can reach you.';
        }

        if ($booking->pickup_lat === null || $booking->pickup_lng === null) {
            return 'This ride has no pickup location, so it cannot be matched.';
        }

        $km = self::kilometers(
            (float) $driver->current_lat,
            (float) $driver->current_lng,
            (float) $booking->pickup_lat,
            (float) $booking->pickup_lng
        );
        $radius = self::nearbyRadiusKm($driver);

        if ($km > $radius) {
            return 'This pickup is '.number_format($km, 1).' km away, outside your '.number_format($radius, 0).' km nearby area.';
        }

        return null;
    }
}
