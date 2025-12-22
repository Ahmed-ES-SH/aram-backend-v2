<?php

namespace App\Http\Services\OrganizationServices;

use App\Http\Traits\ApiResponse;
use App\Models\Organization;
use Illuminate\Support\Facades\Cache;
use Exception;

class FetchOrganizationsData
{
    use ApiResponse;

    public function OrganizationsForSelectionTable($query = null)
    {
        try {

            $orgsQuery = Organization::query();

            if ($query) {
                $orgsQuery->searchNormalized($query);
            }

            $orgs = $orgsQuery->select('id', 'title', 'email', 'phone_number', 'logo', 'status', 'number_of_reservations', 'created_at')
                ->orderBy('created_at', 'desc')
                ->paginate(25);

            if ($orgs->isEmpty()) {
                return $this->successResponse([], 'No organizations found.', 200);
            }


            return $this->paginationResponse($orgs, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getPublicOrganizationsIds()
    {
        try {
            $users = Organization::where('status', 'published')
                ->pluck('id')
                ->map(function ($id) {
                    return ['id' => $id]; // ✅ رجع كائن فيه المفتاح id
                })
                ->values(); // لضبط الاندكس يبدأ من 0

            return $this->successResponse($users, 200);
        } catch (Exception $e) {
            // في حال حدوث خطأ، يتم إرجاع استجابة خطأ
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function organizationWithSelectedData($request)
    {
        try {
            // ✅ Base query
            $orgsQuery = Organization::where('status', 'published')
                ->orderBy('created_at', 'desc')
                ->select('id', 'title', 'logo');

            // ✅ If there's a search query, apply filtering
            if ($request->filled('query')) {
                // ✅ No cache for search results
                $organizations = $orgsQuery->searchNormalized($request->input('query'))->paginate(20);
            } else {
                // ✅ Cached results when no search
                $organizations = Cache::remember('published_organizations', 3, function () use ($orgsQuery) {
                    return $orgsQuery->get();
                });
            }

            return $this->paginationResponse($organizations, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    // ===============================
    // Case 1: Get all organizations
    // ===============================
    public function index($request)
    {
        try {
            // ✅ Validate request inputs
            $request->validate([
                'query' => 'nullable|string',
                'status' => 'nullable|in:published,not_published,under_review',
                'rating' => 'nullable|numeric|min:0|max:5',
                'number_of_reservations' => 'nullable|numeric|min:0',
                'category_id' => 'nullable|string',
                'active' => 'nullable',
            ]);

            // ✅ Base query
            $orgsQuery = Organization::query();

            /**
             * ===============================
             * Case 1: Search Query
             * ===============================
             */
            if ($request->filled('query')) {
                $orgsQuery->searchNormalized($request->input('query'));
            }

            /**
             * ===============================
             * Case 2: Apply Filters
             * ===============================
             */
            if ($request->filled('status')) {
                $orgsQuery->where('status', $request->status);
            }

            if ($request->filled('rating')) {
                $orgsQuery->where('rating', '>=', $request->rating);
            }

            if ($request->filled('number_of_reservations')) {
                $orgsQuery->where('number_of_reservations', '>=', $request->number_of_reservations);
            }

            if ($request->filled('active')) {
                $orgsQuery->where('active', filter_var($request->active, FILTER_VALIDATE_BOOLEAN));
            }

            if ($request->filled('categories')) {
                $categoryIds = explode(',', $request->categories);

                $orgsQuery->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('category_id', $categoryIds);
                });
            }

            /**
             * ===============================
             * Final Query + Eager Loads
             * ===============================
             */
            $orgs = $orgsQuery
                ->withCount(['subCategories', 'benefits'])
                ->with(['keywords', 'categories', 'subCategories', 'benefits'])
                ->orderBy('created_at', 'desc')
                ->paginate(12);

            /**
             * ===============================
             * Transform each organization
             * ===============================
             */
            $orgs->getCollection()->transform(function ($org) {
                // ✅ Add main category
                $org->category = $org->categories->first();

                // ✅ Decode location (new)
                if (isset($org->location)) {
                    $org->location = $this->decodeLocation($org->location);
                }

                return $org;
            });

            if ($orgs->isEmpty()) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($orgs, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // ===============================
    // Case 2: Get active organizations
    // ===============================
    public function activeOrganizations($request)
    {
        try {
            $limit = $request->input('limit', 12);

            // ✅ Get published + active organizations with relationships
            $orgs = Organization::where('status', 'published')
                ->select('id', 'image', 'logo', 'order', 'number_of_reservations', 'rating',  'open_at', 'close_at', 'title', 'description', 'confirmation_price', 'confirmation_status', 'booking_status')
                ->where('active', 1)
                ->with(['categories', 'keywords'])
                ->withCount(['subCategories', 'benefits'])
                ->limit($limit)
                ->orderBy('order', 'asc')
                ->get();

            // ✅ Add main category (first category)
            $orgs->transform(function ($org) {
                $org->category = $org->categories->first();
                return $org;
            });

            return $this->successResponse($orgs, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // ===============================
    // Case 3: Get published organizations with selected data
    // ===============================
    public function publishedOrganizationswithSelectedData($request)
    {
        try {
            $validatedData = $request->validate([
                'query' => 'nullable|string|max:255'
            ]);

            $query = $validatedData['query'] ?? null;
            // جلب البطاقات النشطة مع التصفح (pagination)
            $orgsQuery = Organization::where('status', 'published')
                ->orderBy('order', 'asc')
                ->select('id', 'logo', 'description', 'title', 'email');


            if ($query) {
                $orgsQuery->searchNormalized($query);
            }

            $orgs = $orgsQuery->paginate(20);

            // التحقق إذا كانت البطاقات فارغة
            if ($orgs->total() === 0) {
                return $this->noContentResponse();
            }


            // إرسال البطاقات مع تفاصيل التصفح
            return $this->paginationResponse($orgs, 200);
        } catch (Exception $e) {
            // في حال حدوث خطأ، يتم إرجاع استجابة خطأ
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    // ===============================
    // Case 4: Get top 10 published organizations
    // ===============================
    public function TopTenPublicOrganizations()
    {
        try {
            // ✅ Get top 10 published organizations by rating
            $orgs = Organization::where('status', 'published')
                ->select('id', 'title', 'description', 'image', 'logo', 'number_of_reservations', 'rating', 'location')
                ->with(['categories:id,title_en,title_ar,bg_color,icon_name', 'keywords'])
                ->orderBy('rating', 'desc')
                ->limit(10)
                ->get();

            if ($orgs->isEmpty()) {
                return $this->noContentResponse();
            }

            // ✅ Attach main category (first one from categories)
            $orgs->transform(function ($org) {
                $org->category = $org->categories->first();
                return $org;
            });

            return $this->successResponse($orgs, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    // ===============================
    // Case 5: Get published organizations with filters
    // ===============================
    public function publishedOrganizations($request)
    {
        try {
            // ✅ Validate request inputs
            $request->validate([
                'query' => 'nullable|string',
                'rating' => 'nullable|numeric|min:0|max:5',
                'number_of_reservations' => 'nullable|numeric|min:0',
                'categories' => 'nullable|string',
                'sub_categories' => 'nullable|string',
                'time' => 'nullable|date_format:H:i:s', // ⬅️ وقت معين
                'open_time' => 'nullable|date_format:H:i:s', // ⬅️ بداية نطاق
                'close_time' => 'nullable|date_format:H:i:s', // ⬅️ نهاية نطاق
            ]);

            // ✅ Base query: only published organizations
            $orgsQuery = Organization::where('status', 'published');

            /**
             * ===============================
             * Case 1: Search query
             * ===============================
             */
            if ($request->filled('query')) {
                $orgsQuery->searchNormalized($request->input('query'));
            }

            /**
             * ===============================
             * Case 2: Filters
             * ===============================
             */

            // ✅ Filter by minimum rating
            if ($request->filled('rating')) {
                $orgsQuery->where('rating', '>=', $request->rating);
            }

            // ✅ Filter by minimum reservations
            if ($request->filled('number_of_reservations')) {
                $orgsQuery->where('number_of_reservations', '>=', $request->number_of_reservations);
            }

            // ✅ Filter by categories and subcategories
            if ($request->filled('categories') || $request->filled('sub_categories')) {
                $orgsQuery->where(function ($q) use ($request) {
                    if ($request->filled('categories')) {
                        $categories = explode(',', $request->categories);
                        $q->orWhereHas('categories', function ($query) use ($categories) {
                            $query->whereIn('categories.id', $categories);
                        });
                    }

                    if ($request->filled('sub_categories')) {
                        $subCategories = explode(',', $request->sub_categories);
                        $q->orWhereHas('subCategories', function ($query) use ($subCategories) {
                            $query->whereIn('sub_categories.id', $subCategories);
                        });
                    }
                });
            }

            // ✅ Filter by specific time (organization is open at this time)
            if ($request->filled('time')) {
                $time = $request->input('time');
                $orgsQuery->where('open_at', '<=', $time)
                    ->where('close_at', '>=', $time);
            }

            // ✅ Filter by open/close range
            if ($request->filled('open_time') && $request->filled('close_time')) {
                $orgsQuery->where('open_at', '>=', $request->open_time)
                    ->where('close_at', '<=', $request->close_time);
            }

            /**
             * ===============================
             * Final Query
             * ===============================
             */
            $orgs = $orgsQuery
                ->withCount(['subCategories', 'benefits'])
                ->with(['categories:id,title_en,title_ar,bg_color,icon_name', 'keywords'])
                ->orderBy('order', 'asc')
                ->orderBy('rating', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(12);

            if ($orgs->isEmpty()) {
                return $this->noContentResponse();
            }

            // ✅ Attach main category (first from categories)
            $orgs->getCollection()->transform(function ($org) {
                $org->category = $org->categories->first();
                return $org;
            });

            return $this->paginationResponse($orgs, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // ===============================
    // Case 6: Get organization time work
    // ===============================
    public function getOrgTimeWork($id)
    {
        try {
            $org = Organization::select('open_at', 'close_at', 'confirmation_price', 'confirmation_status')->findOrFail($id);
            return $this->successResponse($org, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // ===============================
    // Case 7: Get organization locations
    // ===============================
    public function getLocations($request)
    {
        try {
            $request->validate([
                'category_id' => 'nullable|exists:categories,id'
            ]);

            $categoryId = $request->category_id;

            $orgsQuery = Organization::query()
                ->where('status', 'published')
                ->select('id', 'location');

            if ($categoryId) {
                $orgsQuery->where('category_id', $categoryId);
            }

            $locations = $orgsQuery->get()->map(function ($org) {
                $location = $org->location;

                // Decode only if it's a string
                if (is_string($location)) {
                    $decoded = json_decode($location, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $location = $decoded;
                    }
                }

                return [
                    'id' => $org->id,
                    'location' => $location, // decoded array or original
                ];
            });

            return $this->successResponse($locations, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
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
