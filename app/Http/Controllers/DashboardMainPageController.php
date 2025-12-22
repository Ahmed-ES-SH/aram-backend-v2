<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Models\Coupon;
use App\Models\Offer;
use App\Models\Organization;
use App\Models\Promoter;
use App\Models\ServicePage;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class DashboardMainPageController extends Controller
{
    use ApiResponse;

    public function getStats()
    {
        try {
            $usersCount = User::count();
            $organizationsCount = Organization::count();
            $promotersCount = Promoter::count();
            $servicesCount = ServicePage::count();
            $offersCount = Offer::count();
            $couponsCount = Coupon::count();

            $data = [
                'usersCount' => $usersCount,
                'organizationsCount' => $organizationsCount,
                'promotersCount' => $promotersCount,
                'servicesCount' => $servicesCount,
                'offersCount' => $offersCount,
                'couponsCount' => $couponsCount,
            ];

            return $this->successResponse($data, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getChartsData()
    {
        try {
            // Arabic months in correct order
            $months = [
                "يناير",
                "فبراير",
                "مارس",
                "أبريل",
                "مايو",
                "يونيو",
                "يوليو",
                "أغسطس",
                "سبتمبر",
                "أكتوبر",
                "نوفمبر",
                "ديسمبر"
            ];

            // Prepare array
            $usersData = [];

            for ($i = 1; $i <= 12; $i++) {

                // Count users per month
                $count = User::whereMonth('created_at', $i)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count();

                $usersData[] = [
                    "month" => $months[$i - 1],
                    "users" => $count,
                ];
            }

            // Fetch services with their orders_count
            $services = ServicePage::select('slug', 'orders_count')
                ->orderBy('orders_count', 'desc') // optional: sort by usage
                ->get();

            // Map to required format
            $servicesData = $services->map(function ($item) {
                return [
                    "service" => Str::limit($item->slug, 10),
                    "usage"   => $item->orders_count,
                ];
            });


            $finalData = [
                "users" => $usersData,
                "services" => $servicesData,
                "organizations" => $this->getCentersDistribution(),
            ];

            return $this->successResponse($finalData, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    private function getCentersDistribution()
    {
        // Fetch all centers with their location
        $centers = Organization::select('location')->get();

        // List of Omani cities with coordinates (Arabic names)
        $cities = [
            "مسقط"              => ["lat" => 23.5880, "lng" => 58.3829],
            "صلالة"             => ["lat" => 17.0194, "lng" => 54.0897],
            "صحار"              => ["lat" => 24.3500, "lng" => 56.7090],
            "نزوى"              => ["lat" => 22.9333, "lng" => 57.5333],
            "صور"               => ["lat" => 22.5667, "lng" => 59.5289],
            "عبري"              => ["lat" => 23.2250, "lng" => 56.5156],
            "بركاء"             => ["lat" => 23.7074, "lng" => 57.8920],
            "الرستاق"           => ["lat" => 23.3908, "lng" => 57.4244],
            "خصب"               => ["lat" => 26.1900, "lng" => 56.2436],
            "البريمي"           => ["lat" => 24.2500, "lng" => 55.7990],
            "بدبد"              => ["lat" => 23.4050, "lng" => 58.1270],
            "آدم"               => ["lat" => 22.3790, "lng" => 57.5270],
            "هيما"              => ["lat" => 19.9980, "lng" => 56.2750],
            "مصيرة"             => ["lat" => 20.6750, "lng" => 58.8900],
            "جعلان بني بو علي"  => ["lat" => 22.0360, "lng" => 59.3300],
            "شناص"             => ["lat" => 24.7426, "lng" => 56.4653],
            "لوى"              => ["lat" => 24.5600, "lng" => 56.5600],
            "محضة"             => ["lat" => 24.3480, "lng" => 55.9730]
        ];

        // Counter for each city
        $cityCount = array_fill_keys(array_keys($cities), 0);

        foreach ($centers as $center) {
            $location = $this->decodeLocation($center->location);

            if (!$location || !isset($location['coordinates'])) {
                continue;
            }

            $lat = $location['coordinates']['lat'];
            $lng = $location['coordinates']['lng'];

            // Find closest city
            $closestCity = null;
            $closestDistance = PHP_INT_MAX;

            foreach ($cities as $cityName => $coords) {
                $distance = $this->distance($lat, $lng, $coords['lat'], $coords['lng']);
                if ($distance < $closestDistance) {
                    $closestDistance = $distance;
                    $closestCity = $cityName;
                }
            }

            if ($closestCity) {
                $cityCount[$closestCity]++;
            }
        }

        // Colors list
        $colors = ["#FF5733", "#33C1FF", "#8D33FF", "#33FF8A", "#FF33A8", "#FFC133"];

        // Format final output
        $formatted = [];
        $index = 0;

        foreach ($cityCount as $city => $count) {
            if ($count == 0) continue;

            $formatted[] = [
                "name"  => $city,
                "value" => $count,
                "color" => $colors[$index % count($colors)]
            ];
            $index++;
        }

        return array_values($formatted);
    }


    /**
     * Decode JSON location
     */



    /**
     * Haversine distance formula
     */
    private function distance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // in KM

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }



    private function decodeLocation($location)
    {
        if (is_string($location)) {
            $decoded = json_decode($location, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $location = $decoded;
            }
        }
        return $location;
    }
}
