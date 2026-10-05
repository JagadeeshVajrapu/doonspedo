<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\DriverRegistration;
use Illuminate\Support\Facades\Cache;

class DriverRideRejections
{
    private const TTL_SECONDS = 43200;

    public static function remember(int $driverId, int $bookingId): void
    {
        $ids = self::idsFor($driverId);
        $ids[] = $bookingId;
        Cache::put(self::key($driverId), array_values(array_unique($ids)), self::TTL_SECONDS);
    }

    public static function idsFor(int $driverId): array
    {
        $ids = Cache::get(self::key($driverId), []);

        return is_array($ids) ? array_values(array_unique(array_map('intval', $ids))) : [];
    }

    public static function activeCategoryId(DriverRegistration $driver): ?int
    {
        $categoryId = $driver->vehicles()->where('status', 'active')->value('vehicle_category_id');

        return $categoryId ? (int) $categoryId : null;
    }

    public static function matchesCategory(DriverRegistration $driver, Booking $booking): bool
    {
        $categoryId = self::activeCategoryId($driver);

        return $categoryId !== null && (int) $booking->vehicle_category_id === $categoryId;
    }

    private static function key(int $driverId): string
    {
        return 'driver_rejected_bookings:'.$driverId;
    }
}
