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

        if (!self::sameOperatingState($driver, $booking)) {
            return 'This pickup is outside your operating state.';
        }

        if ($km > $radius) {
            return 'This pickup is '.number_format($km, 1).' km away, outside your '.number_format($radius, 0).' km nearby area.';
        }

        return null;
    }

    public static function sameOperatingState(?DriverRegistration $driver, Booking $booking): bool
    {
        $driverState = self::driverOperatingState($driver);
        $pickupState = self::pickupState($booking);

        if ($driverState === null || $pickupState === null) {
            return false;
        }

        return strcasecmp($driverState, $pickupState) === 0;
    }

    public static function driverOperatingState(?DriverRegistration $driver): ?string
    {
        if (!$driver) {
            return null;
        }

        $fromCity = self::stateFromText($driver->city);
        if ($fromCity) {
            return $fromCity;
        }

        if ($driver->branch_id) {
            $branch = \App\Models\Branch::query()->find($driver->branch_id);
            if ($branch) {
                $fromBranch = self::stateFromText(trim(($branch->name ?? '').' '.($branch->address ?? '')));
                if ($fromBranch) {
                    return $fromBranch;
                }
            }
        }

        if ($driver->current_lat !== null && $driver->current_lng !== null) {
            return self::stateFromPoint((float) $driver->current_lat, (float) $driver->current_lng);
        }

        return null;
    }

    public static function pickupState(Booking $booking): ?string
    {
        $fromText = self::stateFromText($booking->pickup_location);
        $fromPoint = ($booking->pickup_lat !== null && $booking->pickup_lng !== null)
            ? self::stateFromPoint((float) $booking->pickup_lat, (float) $booking->pickup_lng)
            : null;

        // A stale address label must not hide the ride from drivers at the actual pickup point.
        if ($fromText && $fromPoint && strcasecmp($fromText, $fromPoint) !== 0) {
            return $fromPoint;
        }

        return $fromText ?: $fromPoint;
    }

    public static function stateFromText(?string $value): ?string
    {
        $text = strtolower(trim((string) $value));
        $text = preg_replace('/\s+/', ' ', $text) ?? '';
        if ($text === '') {
            return null;
        }

        $places = self::placeStates();
        uksort($places, function ($a, $b) {
            return strlen($b) <=> strlen($a);
        });

        foreach ($places as $place => $state) {
            $pattern = '/\b'.preg_quote($place, '/').'\b/';
            if (preg_match($pattern, $text)) {
                return $state;
            }
        }

        return null;
    }

    public static function stateFromPoint(?float $lat, ?float $lng): ?string
    {
        if ($lat === null || $lng === null) {
            return null;
        }

        $best = null;
        $bestArea = null;
        foreach (self::stateBoxes() as [$state, $south, $north, $west, $east]) {
            if ($lat < $south || $lat > $north || $lng < $west || $lng > $east) {
                continue;
            }
            $area = ($north - $south) * ($east - $west);
            if ($bestArea === null || $area < $bestArea) {
                $best = $state;
                $bestArea = $area;
            }
        }

        return $best;
    }

    private static function placeStates(): array
    {
        return [
            'uttarakhand' => 'Uttarakhand',
            'uttaranchal' => 'Uttarakhand',
            'dehradun' => 'Uttarakhand',
            'mussoorie' => 'Uttarakhand',
            'rishikesh' => 'Uttarakhand',
            'haridwar' => 'Uttarakhand',
            'roorkee' => 'Uttarakhand',
            'haldwani' => 'Uttarakhand',
            'nainital' => 'Uttarakhand',
            'kashipur' => 'Uttarakhand',
            'rudrapur' => 'Uttarakhand',
            'new delhi' => 'Delhi',
            'delhi' => 'Delhi',
            'greater noida' => 'Uttar Pradesh',
            'noida' => 'Uttar Pradesh',
            'ghaziabad' => 'Uttar Pradesh',
            'lucknow' => 'Uttar Pradesh',
            'agra' => 'Uttar Pradesh',
            'kanpur' => 'Uttar Pradesh',
            'varanasi' => 'Uttar Pradesh',
            'meerut' => 'Uttar Pradesh',
            'uttar pradesh' => 'Uttar Pradesh',
            'gurugram' => 'Haryana',
            'gurgaon' => 'Haryana',
            'faridabad' => 'Haryana',
            'panipat' => 'Haryana',
            'haryana' => 'Haryana',
            'chandigarh' => 'Chandigarh',
            'himachal pradesh' => 'Himachal Pradesh',
            'shimla' => 'Himachal Pradesh',
            'punjab' => 'Punjab',
            'rajasthan' => 'Rajasthan',
            'jaipur' => 'Rajasthan',
            'madhya pradesh' => 'Madhya Pradesh',
            'bhopal' => 'Madhya Pradesh',
            'maharashtra' => 'Maharashtra',
            'mumbai' => 'Maharashtra',
            'pune' => 'Maharashtra',
            'karnataka' => 'Karnataka',
            'bengaluru' => 'Karnataka',
            'bangalore' => 'Karnataka',
            'tamil nadu' => 'Tamil Nadu',
            'chennai' => 'Tamil Nadu',
            'kerala' => 'Kerala',
            'kochi' => 'Kerala',
            'gujarat' => 'Gujarat',
            'ahmedabad' => 'Gujarat',
            'west bengal' => 'West Bengal',
            'kolkata' => 'West Bengal',
            'bihar' => 'Bihar',
            'patna' => 'Bihar',
            'jharkhand' => 'Jharkhand',
            'ranchi' => 'Jharkhand',
            'chhattisgarh' => 'Chhattisgarh',
            'raipur' => 'Chhattisgarh',
            'odisha' => 'Odisha',
            'bhubaneswar' => 'Odisha',
            'andhra pradesh' => 'Andhra Pradesh',
            'telangana' => 'Telangana',
            'hyderabad' => 'Telangana',
            'goa' => 'Goa',
            'assam' => 'Assam',
            'guwahati' => 'Assam',
            'jammu' => 'Jammu and Kashmir',
            'srinagar' => 'Jammu and Kashmir',
        ];
    }

    private static function stateBoxes(): array
    {
        return [
            ['Delhi', 28.50, 28.89, 76.84, 77.35],
            ['Chandigarh', 30.67, 30.80, 76.70, 76.86],
            ['Goa', 14.90, 15.80, 73.68, 74.35],
            ['Uttarakhand', 28.72, 31.45, 77.57, 81.05],
            ['Himachal Pradesh', 30.38, 33.25, 75.55, 79.05],
            ['Punjab', 29.54, 32.57, 73.88, 76.95],
            ['Haryana', 27.65, 30.93, 74.47, 77.60],
            ['Uttar Pradesh', 23.87, 30.40, 77.08, 84.63],
            ['Rajasthan', 23.05, 30.20, 69.48, 78.27],
            ['Maharashtra', 15.60, 22.03, 72.65, 80.90],
            ['Karnataka', 11.55, 18.45, 74.05, 78.59],
            ['Tamil Nadu', 8.07, 13.50, 76.23, 80.35],
            ['Kerala', 8.29, 12.80, 74.86, 77.42],
            ['Gujarat', 20.12, 24.70, 68.16, 74.48],
            ['Madhya Pradesh', 21.08, 26.87, 74.02, 82.81],
            ['Bihar', 24.29, 27.52, 83.32, 88.28],
            ['West Bengal', 21.54, 27.22, 85.82, 89.88],
            ['Telangana', 15.84, 19.92, 77.25, 81.30],
        ];
    }
}
