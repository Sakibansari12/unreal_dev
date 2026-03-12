<?php

namespace App\Http\Controllers\PMS\Property;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TblHome;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Models\TblState;
use App\Models\TblHomeType;
use App\Models\TblLocation;
use App\Models\TblIcon;
use App\Models\TblHomeAmenities;
use App\Models\TblRuAmenity;
use App\Models\HomeAdditionalCharge;
use App\Models\TblHomeImageVideo;
use App\Models\TblRuImageType;
use App\Models\CancellationSlab;
use App\Models\TblRuAmenityMapping;
use App\Services\PropertyService;
use App\Models\RuPropertyPrice;
use App\Models\TblPropertyPublishLog;
use App\Models\RuPropertyAvailability;
use App\Models\RuPropertyMinstay;
use App\Models\TblHomeReview;
use App\Models\TblUnitMultiunit;
use App\Models\WebsiteFaq;
use App\Models\FeaturedPropertySetting;
use Illuminate\Support\Facades\Validator;
use App\Services\OtaServices;



use App\Services\PriceLabsSyncService;
use App\Services\PriceLabsPayloadService;
use App\Services\PriceLabService;
use App\Models\PropertyBooking;


use App\helper\MasterHelper;
use App\Models\TblWebsiteAmenities;
use App\Models\TblHomeTags;
use App\Models\HomeImportantInformation;
use App\Models\TblCollection;
use App\Models\TblReviewImages;
use App\Models\TbllayoutImage;
use App\Models\TblHomeCollection;
use App\Http\Controllers\PMS\Property\SyncPropertyInRuController;
use Session;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;



class UnitOrMultiUnitController extends Controller
{

    protected $propertyService;
    protected $otaService;
    protected $ruSyncController;
    protected $priceLabPayloadService;
    protected $priceLabService;

    public function __construct()
    {
        $this->propertyService = new PropertyService();
        $this->otaService = new OtaServices();
        $this->ruSyncController = new SyncPropertyInRuController();
       


        $this->priceLabPayloadService = new PriceLabsPayloadService();
        $this->priceLabService = new PriceLabService();

    }



    public function publishedPropertyList(Request $request)
    {
        // 🔹 Base query
        $query = TblHomeUnit::with(['imagesDisplaypms']);
        $query = $this->propertyService->applyUserRoleFilter($query);

        // 🔍 Property (unit) name filter
        $query->when($request->filled('property_name'), function ($q) use ($request) {
            $q->where('unit_name', 'like', '%' . $request->property_name . '%');
        });

        // 🔍 Property manager filter (DB LEVEL)
        $query->when($request->filled('property_manager'), function ($q) use ($request) {
            $q->whereHas('home.user', function ($uq) use ($request) {
                $uq->where('role_id', 7)
                    ->where('name', 'like', '%' . $request->property_manager . '%')
                    ->orWhereHas('parent', function ($pq) use ($request) {
                        $pq->where('role_id', 7)
                            ->where('name', 'like', '%' . $request->property_manager . '%');
                    });
            });
        });

        // 🔹 Published only
        $query->where('is_published', 1)
            ->orderBy('id', 'desc');

        // 🔹 Pagination (SAFE)
        $propertList = $query
            ->paginate(30)
            ->withQueryString();

        // 🔹 Attach property manager name (DISPLAY ONLY)
        $propertList->getCollection()->transform(function ($property) {
            $user = $property->home?->user;

            if ($user && $user->role_id == 7) {
                $property->property_manager_name = $user->name;
            } elseif ($user && $user->parent && $user->parent->role_id == 7) {
                $property->property_manager_name = $user->parent->name;
            } else {
                $property->property_manager_name = '';
            }

            return $property;
        });

        // 🔽 Dropdown list
        $propertyDropdownList = TblHomeUnit::with('home.user.parent')
            ->where('is_published', 1)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($property) {
                $user = $property->home?->user;

                if ($user && $user->role_id == 7) {
                    $property->property_manager_name = $user->name;
                } elseif ($user && $user->parent && $user->parent->role_id == 7) {
                    $property->property_manager_name = $user->parent->name;
                } else {
                    $property->property_manager_name = null;
                }

                return $property;
            });

        $user = Auth::guard('admin')->user();

        return view(
            'pms.property.published-property-list',
            compact('propertList', 'propertyDropdownList', 'user')
        );
    }


    // public function featuredPropertyList(Request $request){
    //     /* if( $request->missing('property_id')){
    //       abort('404');
    //     }
    //     if( $request->missing('pType')){
    //         abort('404');
    //     } */
    //     $query = TblHomeUnit::with(['imagesDisplaypms']);
    //     $query = $this->propertyService->applyUserRoleFilter($query);


    //     if ($request->filled('property_name') && $request->property_name !='') {
    //         $query->where('unit_name', 'like', '%' . $request->property_name . '%');
    //     }


    //     $query->where('is_published', 1)->where('show_on_home', 1);
    //     $propertList = $query->orderby('position','asc')->paginate(15)->withQueryString();


    //     $propertList->map(function ($property) {
    //         $parentHome = TblHome::with('user.parent')->where('id', $property->home_id)->first();
    //         $user = $parentHome->user;
    //         if ($user) {
    //             if ($user->role_id == 7) {
    //                 $property->property_manager_name = $user->name;
    //             }
    //             elseif ($user->parent && $user->parent->role_id == 7) {
    //                 $property->property_manager_name = $user->parent->role;
    //             }
    //             else {
    //                 $property->property_manager_name = '';
    //             }
    //         }
    //         else {
    //             $property->property_manager_name = '';
    //         }
    //         return $property;
    //     });


    //     if (!empty($request->property_manager)) {
    //         $filtered = $propertList->filter(function ($home) use ($request) {
    //             return str_contains(strtolower($home->property_manager_name), strtolower($request->property_manager));
    //         });

    //         // Manual pagination
    //         $page = request()->get('page', 1);
    //         $perPage = 15;
    //         $offset = ($page - 1) * $perPage;

    //         $propertList = new LengthAwarePaginator(
    //             $filtered->slice($offset, $perPage)->values(),
    //             $filtered->count(),
    //             $perPage,
    //             $page,
    //             ['path' => request()->url(), 'query' => request()->query()]
    //         );
    //     }


    //     $user =  Auth::guard('admin')->user();
    //     $queryDropDown = TblHomeUnit::query();
    //     $queryDropDown = $this->propertyService->applyUserRoleFilter($queryDropDown);
    //     $propertyDropdownList = $queryDropDown->where('is_published', 1)->where('show_on_home', 1)->orderBy('id', 'desc')->get();

    //     $propertyDropdownList->map(function ($property) {
    //         $parentHome = TblHome::with('user.parent')
    //             ->where('id', $property->home_id)
    //             ->first();

    //         $user = $parentHome?->user;

    //         if ($user && $user->role_id == 7) {
    //             $property->property_manager_name = $user->name;
    //         } elseif ($user && $user->parent && $user->parent->role_id == 7) {
    //             $property->property_manager_name = $user->parent->name;
    //         } else {
    //             $property->property_manager_name = '';
    //         }

    //         return $property;
    //     });



    //     $orderType = FeaturedPropertySetting::first()?->order_type ?? 'fixed';
    //     return view('pms.property.featured-property-list',compact('propertList', /* 'parentHome', */'user', 'propertyDropdownList','orderType'));
    // }



    public function featuredPropertyList(Request $request)
    {
        // 🔹 Base query
        $query = TblHomeUnit::with(['imagesDisplaypms', 'home.user.parent']);
        $query = $this->propertyService->applyUserRoleFilter($query);

        // 🔍 Property (unit) name filter
        $query->when($request->filled('property_name'), function ($q) use ($request) {
            $q->where('unit_name', 'like', '%' . $request->property_name . '%');
        });

        // 🔍 Property manager filter (DB LEVEL)
        $query->when($request->filled('property_manager'), function ($q) use ($request) {
            $q->whereHas('home.user', function ($uq) use ($request) {
                $uq->where('role_id', 7)
                    ->where('name', 'like', '%' . $request->property_manager . '%')
                    ->orWhereHas('parent', function ($pq) use ($request) {
                        $pq->where('role_id', 7)
                            ->where('name', 'like', '%' . $request->property_manager . '%');
                    });
            });
        });

        // 🔹 Featured + Published
        $query->where('is_published', 1)
            ->where('show_on_home', 1)
            ->orderBy('position', 'asc');

        // 🔹 Pagination (SAFE)
        $propertList = $query
            ->paginate(30)
            ->withQueryString();

        // 🔹 Attach property manager name (DISPLAY ONLY)
        $propertList->getCollection()->transform(function ($property) {
            $user = $property->home?->user;

            if ($user && $user->role_id == 7) {
                $property->property_manager_name = $user->name;
            } elseif ($user && $user->parent && $user->parent->role_id == 7) {
                $property->property_manager_name = $user->parent->name;
            } else {
                $property->property_manager_name = '';
            }

            return $property;
        });

        // 🔽 Dropdown list
        $propertyDropdownList = TblHomeUnit::with('home.user.parent')
            ->where('is_published', 1)
            ->where('show_on_home', 1)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($property) {
                $user = $property->home?->user;

                if ($user && $user->role_id == 7) {
                    $property->property_manager_name = $user->name;
                } elseif ($user && $user->parent && $user->parent->role_id == 7) {
                    $property->property_manager_name = $user->parent->name;
                } else {
                    $property->property_manager_name = null;
                }

                return $property;
            });

        $orderType = FeaturedPropertySetting::first()?->order_type ?? 'fixed';
        $user = Auth::guard('admin')->user();

        return view(
            'pms.property.featured-property-list',
            compact('propertList', 'user', 'propertyDropdownList', 'orderType')
        );
    }



    public function savePositionFeatured(Request $request)
    {
        try {

            $positions = $request->position;
            // dd($positions);
            if (!is_array($positions)) {
                throw new \Exception('$positions must be an array.');
            }
            foreach ($positions as $index => $id) {
                TblHomeUnit::where('id', $id)->update(['position' => $index]);
            }
            return response()->json([
                'status' => true,
                'message' => 'Successfully Updated.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function updateOrderType(Request $request)
    {
        $request->validate([
            'order_type' => 'required|in:random,fixed',
        ]);

        // Check if a row already exists
        $setting = FeaturedPropertySetting::first();

        if ($setting) {
            // Update existing row
            $setting->order_type = $request->order_type;
            $setting->save();
        } else {
            // Create new row if not exists
            FeaturedPropertySetting::create([
                'order_type' => $request->order_type,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Order type updated successfully.'
        ]);
    }



    // public function list(Request $request)
    // {
    //     if ($request->missing('property_id')) {
    //         abort('404');
    //     }
    //     if ($request->missing('pType')) {
    //         abort('404');
    //     }
    //     $query = ($request->pType == 'unit') ? TblHomeUnit::query() : TblHomeMultiUnit::query();
    //     $query = $this->propertyService->applyUserRoleFilter($query);
    //     if ($request->filled('search_name')) {
    //         $query->where('unit_name', 'like', '%' . $request->search_name . '%');
    //     }
    //     $query->where('home_id', $request->property_id)->orderBy('id', 'desc');
    //     $propertList = $query->with('primaryImageRelation')->paginate(50)->withQueryString();

    //     $parentHome = TblHome::where('id', $request->property_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);
    //     $user =  Auth::guard('admin')->user();
    //     return view('pms.property.unit-or-multiunit-list', compact('propertList', 'parentHome', 'user'));
    // }

    public function list(Request $request)
    {
        if ($request->missing('property_id')) {
            abort('404');
        }
        if ($request->missing('pType')) {
            abort('404');
        }
        
        ini_set('memory_limit', '15G');
        $query = ($request->pType == 'unit') ? TblHomeUnit::query() : TblHomeMultiUnit::query();
        //$query = $this->propertyService->applyUserRoleFilter($query);
        if ($request->filled('search_name')) {
            $query->where('unit_name', 'like', '%' . $request->search_name . '%');
        }
        $query->where('home_id', $request->property_id)->orderBy('id', 'desc');
        // $propertList = $query->with('primaryImageRelation')->paginate(50)->withQueryString();
        $propertList = $query->paginate(30)->withQueryString();

        $parentHome = TblHome::where('id', $request->property_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);
        $user =  Auth::guard('admin')->user();
        return view('pms.property.unit-or-multiunit-list', compact('propertList', 'parentHome', 'user'));
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            if ($request->pType == 'unit') {
                $detail = TblHomeUnit::where(['id' => $id])->first();
            } else {
                $detail = TblHomeMultiUnit::where(['id' => $id])->first();
            }
            $detail->update(['status' => !$detail->status]);
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Status updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function showOnHome(Request $request, $id)
    {
        try {
            if ($request->pType == 'unit') {
                $detail = TblHomeUnit::where(['id' => $id])->first();
            } else {
                $detail = TblHomeMultiUnit::where(['id' => $id])->first();
            }
            $detail->update(['show_on_home' => !$detail->show_on_home]);
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Status updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function showOnOnlyforEnquiry(Request $request, $id)
    {
        try {
            if ($request->pType == 'unit') {
                $detail = TblHomeUnit::where(['id' => $id])->first();
            } else {
                $detail = TblHomeMultiUnit::where(['id' => $id])->first();
            }
            $detail->update(['only_for_enquiry' => !$detail->only_for_enquiry]);
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Only For Enquiry updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request, $id)
    {
        try {

            if ($request->pType == 'unit') {
                $detail = TblHomeUnit::where(['id' => $id])->first();
            } else {
                $detail = TblHomeMultiUnit::where(['id' => $id])->first();
            }

            if ($detail->ru_property_id) {
                $xml = "<Push_SetPropertiesStatus_RQ>
                          <Authentication>
                              <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                              <Password>" . config('ru.RU_PASSWORD') . "</Password>
                          </Authentication>
                          <IsActive>0</IsActive>
                          <IsArchived>1</IsArchived>
                          <PropertyIDs>
                              <PropertyID>" . $detail->ru_property_id . "</PropertyID>
                          </PropertyIDs>
                 </Push_SetPropertiesStatus_RQ>";
                $xmlResponse = MasterHelper::makeXmlRequest($xml);
            }
            $detail->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Property deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function multiDelete(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'status' => false,
                'message' => 'No roles selected for deletion.'
            ], 400);
        }
        try {
            if ($request->pType == 'unit') {
                $detail = TblHomeUnit::whereIn(['id' => $ids])->get();
            } else {
                $detail = TblHomeMultiUnit::whereIn(['id' => $ids])->get();
            }
            $detail->delete();
            return response()->json([
                'status' => true,
                'message' => 'Role(s) deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // -----------------preview list------------------//
    public function preview(Request $request, $id = null)
    {
        abort_if($request->missing('pType'), 404);
        $detail = $request->pType === 'unit' ? TblHomeUnit::findOrFail($id) : TblHomeMultiUnit::findOrFail($id);
        $parentHome = TblHome::where('id', $detail->home_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);

        $isPublishBtn = false;
        $isPublishGallery = false;
        $isAmmenitiesUploaded = false;
        $isRoomSpecificAmmenitiesUploaded = false;
        $isCancellationSlabUploaded = false;

        if ($request->pType == 'unit') {
            $galleryCount = TblHomeImageVideo::where('unit_id', $id)->count();
        } else {
            $galleryCount = TblHomeImageVideo::where('multi_unit_id', $id)->count();
        }
        if ($galleryCount >= 10) {
            $isPublishGallery = true;
        }

        if ($request->pType == 'unit') {
            $galleryMainCount = TblHomeImageVideo::where('unit_id', $id)->where('title', 'Main Image')->count();
        } else {
            $galleryMainCount = TblHomeImageVideo::where('multi_unit_id', $id)->where('title', 'Main Image')->count();
        }

        if ($galleryMainCount == 0) {
            $isPublishGallery = false;
        }

        if ($request->pType == 'unit') {
            $savedAmmentiesArray = TblHomeAmenities::where('unit_id', $id)->get();
        } else {
            $savedAmmentiesArray = TblHomeAmenities::where('multi_unit_id', $id)->get();
        }

        if ($savedAmmentiesArray->count() > 0) {
            $isAmmenitiesUploaded = true;
        }

        if ($request->pType == 'unit') {
            $roomSpecificAmmentires = TblRuAmenityMapping::where('unit_id', $id)->get();
        } elseif ($request->pType == 'multiunit') {
            $roomSpecificAmmentires = TblRuAmenityMapping::where('multi_unit_id', $id)->get();
        }

        if ($roomSpecificAmmentires->count() > 0) {
            $isRoomSpecificAmmenitiesUploaded = true;
        }

        $cancellation_slab = ($request->pType == 'unit') ? CancellationSlab::where('unit_id', $id)->get() : CancellationSlab::where('multi_unit_id', $id)->get();

        if ($cancellation_slab->count() > 0) {
            $isCancellationSlabUploaded  = true;
        }

        $detail->isOverViewUploaded = $isPublishGallery;
        $detail->isAmmenitiesUploaded = $isAmmenitiesUploaded;
        $detail->isRoomSpecificAmmenitiesUploaded = $isRoomSpecificAmmenitiesUploaded;
        $detail->isCancellationSlabUploaded = $isCancellationSlabUploaded;
        $detail->isGalleryUploaded = $isPublishGallery;

        if ($isPublishGallery && $isAmmenitiesUploaded && $isRoomSpecificAmmenitiesUploaded && $isCancellationSlabUploaded) {
            $isPublishBtn = true;
        }

        $html = view('pms.property.preview', [
            'detail' => $detail,
            'parentHome' => $parentHome,
            'isPublishBtn' => $isPublishBtn
        ])->render();

        return response()->json([
            'detail' => $detail,
            'parentHome' => $parentHome,
            'html' => $html
        ]);
    }

    public function publish(Request $request, $id = null)
    {
        $ruResponse = $this->otaService->pushPropertyInOta($id, $request->pType);

        $query = ($request->pType == 'unit') ? TblHomeUnit::query() : TblHomeMultiUnit::query();
        $property = $query->find($id);
        if ($property->is_published == 1) {
            $property->ru_status = 1;
            $property->is_published_date = now();
            $property->save();
        }


        $response = $ruResponse->getData();
        return response()->json([
            'success' => $response->status,
            'message' => $response->message,
        ], 200);
    }



    public function websitePreview(Request $request, $id = null)
    {
        abort_if($request->missing('pType'), 404);
        $detail = $request->pType === 'unit' ? TblHomeUnit::findOrFail($id) : TblHomeMultiUnit::findOrFail($id);
        $parentHome = TblHome::where('id', $detail->home_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);

        $isPublishBtn = false;
        $isPublishGallery = false;
        $isAmmenitiesUploaded = false;
        $isRoomSpecificAmmenitiesUploaded = false;
        $isCancellationSlabUploaded = false;

        if ($request->pType == 'unit') {
            $galleryCount = TblHomeImageVideo::where('unit_id', $id)->count();
        } else {
            $galleryCount = TblHomeImageVideo::where('multi_unit_id', $id)->count();
        }
        if ($galleryCount >= 10) {
            $isPublishGallery = true;
        }

        if ($request->pType == 'unit') {
            $galleryMainCount = TblHomeImageVideo::where('unit_id', $id)->where('title', 'Main Image')->count();
        } else {
            $galleryMainCount = TblHomeImageVideo::where('multi_unit_id', $id)->where('title', 'Main Image')->count();
        }

        if ($galleryMainCount == 0) {
            $isPublishGallery = false;
        }

        if ($request->pType == 'unit') {
            $savedAmmentiesArray = TblHomeAmenities::where('unit_id', $id)->get();
        } else {
            $savedAmmentiesArray = TblHomeAmenities::where('multi_unit_id', $id)->get();
        }

        if ($savedAmmentiesArray->count() > 0) {
            $isAmmenitiesUploaded = true;
        }

        if ($request->pType == 'unit') {
            $roomSpecificAmmentires = TblRuAmenityMapping::where('unit_id', $id)->get();
        } elseif ($request->pType == 'multiunit') {
            $roomSpecificAmmentires = TblRuAmenityMapping::where('multi_unit_id', $id)->get();
        }

        if ($roomSpecificAmmentires->count() > 0) {
            $isRoomSpecificAmmenitiesUploaded = true;
        }

        $cancellation_slab = ($request->pType == 'unit') ? CancellationSlab::where('unit_id', $id)->get() : CancellationSlab::where('multi_unit_id', $id)->get();

        if ($cancellation_slab->count() > 0) {
            $isCancellationSlabUploaded  = true;
        }

        $detail->isOverViewUploaded = $isPublishGallery;
        $detail->isAmmenitiesUploaded = $isAmmenitiesUploaded;
        $detail->isRoomSpecificAmmenitiesUploaded = $isRoomSpecificAmmenitiesUploaded;
        $detail->isCancellationSlabUploaded = $isCancellationSlabUploaded;
        $detail->isGalleryUploaded = $isPublishGallery;

        if ($isPublishGallery && $isAmmenitiesUploaded && $isRoomSpecificAmmenitiesUploaded && $isCancellationSlabUploaded) {
            $isPublishBtn = true;
        }

        $html = view('pms.property.website-preview', [
            'detail' => $detail,
            'parentHome' => $parentHome,
            'isPublishBtn' => $isPublishBtn
        ])->render();

        return response()->json([
            'detail' => $detail,
            'parentHome' => $parentHome,
            'html' => $html
        ]);
    }

    public function websitePublish(Request $request, $id = null)
    {
        $websiteResponse = $this->otaService->websitepushProperty($id, $request->pType);

        //  $query = ($request->pType == 'unit')?TblHomeUnit::query():TblHomeMultiUnit::query();
        // $property = $query->find($id);
        // if ($property->is_published == 1) {
        //     $property->ru_status = 1; 
        //     $property->is_published_date = now(); 
        //     $property->save();
        // }

        $response = $websiteResponse->getData();
        return response()->json([
            'success' => $response->status,
            'message' => $response->message,
        ], 200);
    }




    //      public function unpublish(Request $request, $id)
    // {
    //     try {
    //         $detail = ($request->pType == 'unit')
    //             ? TblHomeUnit::find($id)
    //             : TblHomeMultiUnit::find($id);

    //         if (!$detail || !$detail->ru_property_id) {
    //             return response()->json(['status' => false, 'message' => 'Invalid property'], 400);
    //         }

    //         $newStatus = !$detail->ru_status;

    //         $detail->update([
    //             'ru_status' => $newStatus,
    //         ]);

    //         if($newStatus == 0){
    //             $today = now()->toDateString();
    //             $exists = TblPropertyPublishLog::where('property_id', $detail->id)
    //                 ->where('pType', $request->pType)
    //                 ->whereDate('adate2', $today)
    //                 ->exists();

    //             if (! $exists) {
    //                 TblPropertyPublishLog::create([
    //                     'property_id' => $detail->id,
    //                     'pType' => $request->pType,
    //                     'status' => $newStatus,
    //                     'activity_date' => now(),
    //                     'adate1' => now(),
    //                 ]);
    //             }

    //             $detaillog2 = TblPropertyPublishLog::whereDate('adate2', $today)->where('property_id', $detail->id)->where('pType', $request->pType)->first();
    //             if($detaillog2){
    //                 $detaillog2->update([
    //                     'adate2' => null,
    //                 ]);
    //             }


    //         }
    //         else{
    //             $detaillog = TblPropertyPublishLog::where('property_id', $detail->id)->where('pType', $request->pType)->whereNull('adate2')->whereNotNull('adate1')->first();
    //             if($detaillog){
    //                 $detaillog->update([
    //                     'adate2' => now(),
    //                 ]);
    //             }
    //         }


    //             $ruActiveStatus = 1;
    //             $ruArchiveStatus = 0;

    //             if($detail->ru_status == 1){
    //                  $ruActiveStatus = 1;
    //                  $ruArchiveStatus = 0;
    //             }
    //             else{
    //                   $ruActiveStatus = 0;
    //                   $ruArchiveStatus = 1;
    //             }

    //             $xml = "<Push_SetPropertiesStatus_RQ>
    //                      <Authentication>
    //                          <UserName>".config('ru.RU_USER_NAME')."</UserName>
    //                          <Password>".config('ru.RU_PASSWORD')."</Password>
    //                      </Authentication>
    //                      <IsActive>".$ruActiveStatus."</IsActive>
    //                      <IsArchived>".$ruArchiveStatus."</IsArchived>
    //                      <PropertyIDs>
    //                          <PropertyID>".$detail->ru_property_id."</PropertyID>
    //                      </PropertyIDs>
    //             </Push_SetPropertiesStatus_RQ>";
    //             $xmlResponse = MasterHelper::makeXmlRequest($xml);



    //         return response()->json([
    //             'status' => true,
    //             'message' => $newStatus ? 'Property published successfully.' : 'Property unpublished successfully.'
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json(['status' => false, 'message' => 'Error'], 500);
    //     }
    // }

    public function unpublish(Request $request, $id)
    {
        try {
            $detail = ($request->pType == 'unit')
                ? TblHomeUnit::find($id)
                : TblHomeMultiUnit::find($id);

            if (!$detail || !$detail->ru_property_id) {
                return response()->json(['status' => false, 'message' => 'Invalid property'], 400);
            }
            $newStatus = !$detail->ru_status;
            $detail->update([
                'ru_status' => $newStatus,
            ]);

            TblPropertyPublishLog::create([
                'property_id' => $detail->id,
                'pType' => $request->pType,
                'status' => $newStatus,
                'activity_date' => now(),
            ]);


            $ruActiveStatus = 1;
            $ruArchiveStatus = 0;

            if ($detail->ru_status == 1) {
                $ruActiveStatus = 1;
                $ruArchiveStatus = 0;
            } else {
                $ruActiveStatus = 0;
                $ruArchiveStatus = 1;
            }

            $xml = "<Push_SetPropertiesStatus_RQ>
                      <Authentication>
                          <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                          <Password>" . config('ru.RU_PASSWORD') . "</Password>
                      </Authentication>
                      <IsActive>" . $ruActiveStatus . "</IsActive>
                      <IsArchived>" . $ruArchiveStatus . "</IsArchived>
                      <PropertyIDs>
                          <PropertyID>" . $detail->ru_property_id . "</PropertyID>
                      </PropertyIDs>
             </Push_SetPropertiesStatus_RQ>";
            $xmlResponse = MasterHelper::makeXmlRequest($xml);


            return response()->json([
                'status' => true,
                'message' => $newStatus ? 'Property published successfully.' : 'Property unpublished successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error'], 500);
        }
    }



    private function getRoomCount($unitId, $totalRooms, $amenityType, $roomNumberColumn, $keyName)
    {
        $selectedRooms = TblRuAmenityMapping::where('unit_id', $unitId)
            ->where('amenity_type', $amenityType)
            ->groupBy($roomNumberColumn)
            ->pluck($roomNumberColumn);

        $roomCount = [];
        foreach ($selectedRooms as $index => $roomNo) {
            $amenities = TblRuAmenityMapping::where('unit_id', $unitId)
                ->where($roomNumberColumn, $index + 1)
                ->where('amenity_type', $amenityType)
                ->pluck('ru_amenity_id')
                ->toArray();
            $roomCount[] = [$keyName => $amenities];
        }

        // Pad with empty arrays if fewer rooms have amenities
        return array_pad($roomCount, $totalRooms, [$keyName => []]);
    }

    // -----------------overview list------------------//
    public function overview(Request $request, $id = null)
    {

        if ($request->missing('pType')) {
            abort('404');
        }
        if ($request->has('str') && $request->str == 'new') {
            $parentHome = TblHome::where('id', $request->property_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);
            $detail = $parentHome;
            // dd($detail);
        } else {
            // $detail = ($request->pType =='unit')?TblHomeUnit::find($id):TblHomeMultiUnit::find($id);
            $detail = ($request->pType == 'unit') ? TblHomeUnit::with('homecollections')->find($id) : TblHomeMultiUnit::with('homecollections')->find($id);
        }
        abort_unless($detail, 404);
        if ($request->missing('str')) {
            $parentHome = TblHome::where('id', $detail->home_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);
        }

        //  $ruResponse = $this->otaService->pushAvaliabilityPriceMinstay($detail->ru_property, 'unit');

        $states = TblState::where('status', 1)->get();
        $homeTypes = TblHomeType::where('status', 1)->get();
        $locations = [];
        if ($detail && $detail->state_id) {
            $locations = TblLocation::where('state_id', $detail->state_id)->where('status', 1)->get();
        }
        $timeslots = collect(range(0, 23))->map(fn($h) => sprintf('%02d:00', $h))->toArray();

        $unitList = collect(array());
        $mappedUnites = array();
        if ($request->pType == 'multiunit') {
            $unitList = TblHomeUnit::where('home_id', $request->property_id)->get(['id', 'unit_name']);
            $mappedUnites = TblUnitMultiunit::where('multiunit_id', $detail->id)->get()->pluck('unit_id')->toArray();
        }

        $reqParameters = $request->all();
        $collectionData = TblCollection::whereNull('deleted_at')->get();
        return view('pms.property.overview', compact('detail', 'states', 'collectionData', 'homeTypes', 'locations', 'id', 'parentHome', 'timeslots', 'unitList', 'reqParameters', 'mappedUnites'));
    }

    public function overviewSave(Request $request)
    {

        $rules = [
            'home_name' => ['required', 'string', 'max:50', 'regex:/^[^,.&]+$/'],
            'unit_name_website' => 'required|string|max:255',
            'property_size' => 'required|numeric',
            'area_unit' => 'required|string',
            'descriptionm' => 'required|string',
            'no_of_rooms' => 'required|integer',
            'maximum_number_of_guests' => 'required|integer',
            'extra_guest_charge' => 'required|numeric',
            // 'per_pet_charge' => 'required|numeric',
            'no_of_staff' => 'required|integer',
            'bedroom' => 'required|integer',
            'bathroom' => 'required|integer',
            'arrival_time' => 'required|string',
            'departure_time' => 'required|string',
            'ru_description' => 'required|string',
            'property_rules' => 'required|string',
            'capacity' => 'required|integer',
            'property_id' => 'required|exists:tbl_homes,id',
            'pType' => 'required|in:unit,multiunit',
            // 'cancellation_policy' => 'required',
            // 'website_property_rules' => 'required'


        ];
        if (!$request->id) {
            $rules['min_stay'] = 'required|numeric';
            $rules['per_night_price'] = 'required|numeric';
        }
        if ($request->pType == 'multiunit') {
            $rules['mappedProperties'] = 'required|array';
        }

        $validated = $request->validate($rules);

        $parentHome = TblHome::findOrFail($validated['property_id']);
        $home = $request->id ? ($validated['pType'] === 'unit' ? TblHomeUnit::find($request->id) : TblHomeMultiUnit::find($request->id)) : ($validated['pType'] === 'unit' ? new TblHomeUnit : new TblHomeMultiUnit);

        $user = Auth::guard('admin')->user();
        $home->unit_name = $validated['home_name'];
        $home->unit_name_website = $validated['unit_name_website'];
        $home->unit_subtitle_website = $request->unit_subtitle_website;
        $home->description = $validated['descriptionm'] ?? null;
        $home->home_id = $parentHome->id;
        $home->state_id = $parentHome->state_id;
        $home->state = $parentHome->state;
        $home->website_property_rules = $request->website_property_rules;
        $home->meals = $request->meals;
        $home->location_id = $parentHome->location_id;
        $home->location = $parentHome->location;
        $home->area_id = $parentHome->area_id;
        $home->area = $parentHome->area;
        $home->home_type_id = $parentHome->home_type_id;
        $home->home_type = $parentHome->home_type;
        $home->map_latitude = $parentHome->map_latitude;
        $home->map_longitude = $parentHome->map_longitude;

        if ($request->id == '') {
            $home->user_id  = $user->id;
            $home->parent_user_id = ($user->role_id == 7) ? $user->id : $user->parent_user_id;
        }

        $home->address = $validated['address'] ?? null;
        $home->postal_code = $parentHome->postal_code;
        $home->no_of_rooms = $validated['no_of_rooms'] ?? null;

        if ($request->has('min_stay')) {
            $home->min_stay = $validated['min_stay'] ?? null;
        }
        if ($request->has('per_night_price')) {
            $home->per_night_price = $validated['per_night_price'] ?? null;
        }
        $home->maximum_number_of_guests = $validated['maximum_number_of_guests'] ?? null;
        $home->extra_guest_charges = $validated['extra_guest_charge'] ?? null;
        // $home->per_pet_charge = $validated['per_pet_charge'] ?? null;
        $home->no_of_staff = $validated['no_of_staff'] ?? null;
        $home->no_of_bedrooms = $validated['bedroom'] ?? null;
        $home->no_of_bathrooms = $validated['bathroom'] ?? null;
        $home->property_size = $validated['property_size'] ?? null;
        $home->area_unit = $validated['area_unit'] ?? null;
        $home->checkin_time = $validated['arrival_time'] ?? null;
        $home->checkout_time = $validated['departure_time'] ?? null;
        $home->ru_description = $validated['ru_description'] ?? null;
        // $home->cancellation_policy = $request->cancellation_policy ?? null;
        $home->house_rules = $validated['property_rules'] ?? null;
        $home->guests_included = $validated['capacity'] ?? null;
        $home->url_key = Str::slug($validated['home_name']);
        $home->slug = Str::slug($validated['home_name']);
        $home->save();

        if (!$request->id) {
            $home->pricelabs_unique_id = 'PROP' . $home->id . collect(range(0, 9))->shuffle()->take(6)->implode('');
            $home->save();
        }





        /* if (isset($request->mappedCollections) && is_array($request->mappedCollections)) {
            TblHomeCollection::where('pType', $request->pType)->where('home_id', $home->id)->delete();
            foreach ($request->mappedCollections as $mapped_collection) {
                TblHomeCollection::create([
                    'home_id' => $home->id,
                    'pType'    => $request->pType,
                    'collection_id' => $mapped_collection,
                ]);
            }
        } */

        // dd($saveResult);
        if ($request->pType == 'multiunit') {
            TblUnitMultiunit::where('multiunit_id', $home->id)->forceDelete();
            foreach ($request->mappedProperties as $value) {
                $detail = array('unit_id' => $value, 'multiunit_id' => $home->id);
                TblUnitMultiunit::create($detail);
            }
        }
        if ($request->id == '') {
            if (isset($request->per_night_price)) {
                $priceArray = array('price' => $request->per_night_price, 'property_id' => $home->id, 'ru_property_id' => $home->id);
                for ($i = 0; $i <= 180; $i++) {
                    $price_date = date('Y-m-d', strtotime(date('Y-m-d') . ' +' . $i . ' day'));
                    $priceArray['price_date'] = $price_date;
                    $priceArray['type'] = $validated['pType'];
                    RuPropertyPrice::create($priceArray);
                }
            }
            $avaliabilityArray = array('ru_property_id' => $home->id, 'is_available' => 'yes', 'property_id' => $home->id);
            for ($i = 0; $i <= 180; $i++) {
                $date =
                    $avaliabilityDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +' . $i . ' day'));
                $avaliabilityArray['availability_date'] = $avaliabilityDate;
                $avaliabilityArray['type'] = $validated['pType'];
                RuPropertyAvailability::create($avaliabilityArray);
            }
            $minStayArray = array('ru_property_id' => $home->id, 'is_minstay_count' => $request->min_stay, 'home_id' => $home->id);
            for ($i = 0; $i <= 180; $i++) {
                $date = date('Y-m-d', strtotime(date('Y-m-d') . ' +' . $i . ' day'));
                $minStayArray['minstay_date'] = $date;
                $minStayArray['type'] = $validated['pType'];
                RuPropertyMinstay::create($minStayArray);
            }
        }

        if(isPriceLabEnable() && $home->price_lab_sync_date_time){
            $listingPayload  =  $this->priceLabPayloadService->preparePriceLabsListingPayload($home);
            $response  =  $this->priceLabService->syncListings($listingPayload);
        }

        return redirect()->route('pms.property.unit.or.multiunit.overview', ['id' => $home->id, 'property_id' => $request->property_id, 'pType' => $request->pType,])->with('success', 'Property overview saved successfully.');
    }


    //-------------------amenities list--------------------//
    public function amenities(Request $request, $id = null)
    {
        $detail = ($request->pType == 'unit') ? TblHomeUnit::find($id) : TblHomeMultiUnit::find($id);
        abort_unless($detail, 404);
        $parentHome = TblHome::where('id', $detail->home_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);

        $savedAmmentiesArray = array();
        if ($id) {
            if ($request->pType == 'unit') {
                $savedAmmentiesArray = TblHomeAmenities::where('unit_id', $id)->get()->pluck('amenities_id')->toArray();
            } else {
                $savedAmmentiesArray = TblHomeAmenities::where('multi_unit_id', $id)->get()->pluck('amenities_id')->toArray();
            }
        }
        $amenitiesList = TblRuAmenity::where('active', 1)->select('amenities_type', 'amenities_name', 'amenities_id', 'ru_amenities_id')->where('status', 2)->orderBy('amenities_type')->orderBy('amenities_name')->get()->groupBy('amenities_type')
            ->map(function ($items, $type) use ($savedAmmentiesArray) {
                return [
                    'type' => $type,
                    'ammenites' => $items->map(function ($item) use ($savedAmmentiesArray) {
                        if (in_array($item->amenities_id, $savedAmmentiesArray)) {
                            $this->selectedAmenities[] = $item->amenities_id;
                        }
                        return [
                            'amenities_id' => $item->amenities_id,
                            'ru_amenities_id' => $item->ru_amenities_id,
                            'amenities_name' => $item->amenities_name,
                            'is_checked' => in_array($item->amenities_id, $savedAmmentiesArray) ? true : false
                        ];
                    })->toArray()
                ];
            })->values()->toArray();
        return view('pms.property.amenities', compact('amenitiesList', 'parentHome', 'id', 'detail'));
    }


    //--------------------Save amenities------------------------------//
    public function amenitiesSave(Request $request)
    {
        $request->validate([
            'selectedAmenities' => 'required|array|min:1',
            'id' => 'required|integer',
            'pType' => 'required|in:unit,multiunit',
        ]);
        $selectedAmenities = $request->selectedAmenities;
        $unitId = $request->id;
        $pType = $request->pType;


        if ($pType === 'unit') {
            TblHomeAmenities::where('unit_id', $unitId)->forceDelete();
        } else {
            TblHomeAmenities::where('multi_unit_id', $unitId)->forceDelete();
        }
        foreach ($selectedAmenities as $amenityId) {
            TblHomeAmenities::create([
                'amenities_id' => $amenityId,
                'add_ip' => $request->ip(),
                'add_by' => 1,
                'unit_id' => $pType === 'unit' ? $unitId : null,
                'multi_unit_id' => $pType === 'multiunit' ? $unitId : null,
                'pType' => $pType
            ]);
        }
        return redirect()->route('pms.property.unit.or.multiunit.amenities', ['id' => $request->id, 'property_id' => $request->property_id, 'pType' => $request->pType,])->with('success', 'Property amenities saved successfully.');
    }


    //-------------------tag list--------------------//
    public function tag(Request $request, $id = null)
    {

        $allData = MasterHelper::getTag();
        if ($request->pType == 'unit') {
            $homeAmenities = TblHomeTags::where('unit_id', $id)
                ->get(['tags_id', 'position'])
                ->keyBy('tags_id')
                ->toArray();
        } else {
            $homeAmenities = TblHomeTags::where('multi_unit_id', $id)
                ->get(['tags_id', 'position'])
                ->keyBy('tags_id')
                ->toArray();
        }
        foreach ($allData as &$amenity) {
            $amenityId = is_array($amenity) ? $amenity['id'] : $amenity->id;
            if (isset($homeAmenities[$amenityId])) {
                $amenity['isChecked'] = true;
                $amenity['position'] = $homeAmenities[$amenityId]['position'];
            } else {
                $amenity['isChecked'] = false;
                $amenity['position'] = PHP_INT_MAX;
            }
        }
        usort($allData, function ($a, $b) {
            return $a['position'] <=> $b['position'];
        });
        // $parentHome = TblHome::where('id', $request->property_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);

        $detail = ($request->pType == 'unit') ? TblHomeUnit::find($id) : TblHomeMultiUnit::find($id);
        $parentHome = TblHome::where('id', $detail->home_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);

        return view('pms.property.tag', compact('allData', 'id', 'parentHome', 'detail'));
    }


    public function tagSave(Request $request)
    {

        $id = $request->home_id;
        $amenities = $request->amenities;

        if ($request->pType == 'unit') {
            TblHomeTags::where('unit_id', $id)->delete();
        } else {
            TblHomeTags::where('multi_unit_id', $id)->delete();
        }

        foreach ($amenities as $key => $objAmenities) {
            if (!isset($objAmenities['isChecked'])) continue;

            $SQL = new TblHomeTags();

            if ($request->pType == 'unit') {
                $SQL->unit_id = $id;
                $SQL->pType = 'unit';
            } elseif ($request->pType == 'multiunit') {
                $SQL->multi_unit_id = $id;
                $SQL->pType = 'multiunit';
            } else {
                $SQL->home_id = $id;
                $SQL->pType = 'standalone';
            }

            $SQL->tags_id = $objAmenities['tags_id'];
            $SQL->tags_name = $objAmenities['tags_name'];
            $SQL->tags_number = $objAmenities['tags_number'] ?? 0;
            $SQL->position = $objAmenities['position'] ?? $key;
            // $SQL->add_ip = $request->ip();
            // $SQL->add_by = Auth::guard('admin')->user()->name;
            $SQL->save();
        }
        return redirect()->back()->with('success', 'Tags saved successfully!');
    }

    // ================= ICON PAGE =================
    public function icon(Request $request, $id)
    {
        $pType = $request->pType; // unit | multiunit

        $detail = ($pType === 'unit')
            ? TblHomeUnit::findOrFail($id)
            : TblHomeMultiUnit::findOrFail($id);

        $parentHome = TblHome::where('id', $detail->home_id)
            ->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);

        $icons = TblIcon::where('status', 1)
            ->orderBy('icons_name')
            ->get();

        // Decide reference column
        $refColumn = $pType === 'unit' ? 'unit_id' : 'multi_unit_id';

        $houseRules = HomeImportantInformation::where([
            'home_id'     => $detail->home_id,
            'pType'       => $pType,
            'type_option' => 'house_rules',
            $refColumn    => $id,
        ])->get();

        $safety = HomeImportantInformation::where([
            'home_id'     => $detail->home_id,
            'pType'       => $pType,
            'type_option' => 'safety_property',
            $refColumn    => $id,
        ])->get();

        $cancellation = HomeImportantInformation::where([
            'home_id'     => $detail->home_id,
            'pType'       => $pType,
            'type_option' => 'cancellation_policy',
            $refColumn    => $id,
        ])->get();

        return view('pms.property.icon', compact(
            'id',
            'detail',
            'parentHome',
            'icons',
            'houseRules',
            'safety',
            'cancellation'
        ));
    }

    public function saveHomeImportantInformation(Request $request)
    {
        try {

            $homeId = $request->home_id;
            $pType  = $request->pType;   // unit | multiunit
            $refId  = $request->id;      // unit_id OR multi_unit_id

            $unitId = $pType === 'unit' ? $refId : 0;
            $multiUnitId = $pType === 'multiunit' ? $refId : 0;

            $types = ['house_rules', 'safety_property', 'cancellation_policy'];

            foreach ($types as $type) {

                // If tab not submitted → delete all rows for that tab
                if (
                    !isset($request->important_information[$type]) ||
                    count($request->important_information[$type]) === 0
                ) {
                    HomeImportantInformation::where([
                        'home_id'     => $homeId,
                        'type_option' => $type,
                        'pType'       => $pType,
                    ])->delete();

                    continue;
                }

                $savedIds = [];

                foreach ($request->important_information[$type] as $row) {

                    $record = HomeImportantInformation::updateOrCreate(
                        ['id' => $row['id'] ?? null],
                        [
                            'home_id'       => $homeId,
                            'pType'         => $pType,
                            'unit_id'       => $unitId,
                            'multi_unit_id' => $multiUnitId,
                            'icon_id'       => $row['icon_id'] ?? 0,
                            'title'         => $row['title'] ?? null,
                            'sub_title'     => $row['sub_title'] ?? null,
                            'type_option'   => $type,
                            'display_on_website' => 1,
                            'status'        => 1,
                        ]
                    );

                    $savedIds[] = $record->id;
                }

                // delete removed rows
                HomeImportantInformation::where([
                    'home_id'     => $homeId,
                    'type_option' => $type,
                    'pType'       => $pType,
                ])
                    ->whereNotIn('id', $savedIds)
                    ->delete();
            }

            return back()
                ->with('success', 'Saved successfully')
                ->with('active_tab', $request->type);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    //-------------------website amenities list--------------------//
    public function websiteAmenities(Request $request, $id = null)
    {

        $allData = MasterHelper::getAmenities();
        if ($request->pType == 'unit') {
            $homeAmenities = TblWebsiteAmenities::where('unit_id', $id)
                ->get(['amenities_id', 'position'])
                ->keyBy('amenities_id')
                ->toArray();
        } else {
            $homeAmenities = TblWebsiteAmenities::where('multi_unit_id', $id)
                ->get(['amenities_id', 'position'])
                ->keyBy('amenities_id')
                ->toArray();
        }
        foreach ($allData as &$amenity) {
            $amenityId = is_array($amenity) ? $amenity['id'] : $amenity->id;
            if (isset($homeAmenities[$amenityId])) {
                $amenity['isChecked'] = true;
                $amenity['position'] = $homeAmenities[$amenityId]['position'];
            } else {
                $amenity['isChecked'] = false;
                $amenity['position'] = PHP_INT_MAX;
            }
        }
        usort($allData, function ($a, $b) {
            return $a['position'] <=> $b['position'];
        });
        // $parentHome = TblHome::where('id', $request->property_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);

        $detail = ($request->pType == 'unit') ? TblHomeUnit::find($id) : TblHomeMultiUnit::find($id);
        $parentHome = TblHome::where('id', $detail->home_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);

        return view('pms.property.website-amenities', compact('allData', 'id', 'parentHome'));
    }


    public function websiteamenitiesSave(Request $request)
    {

        $id = $request->home_id;
        $amenities = $request->amenities;

        if ($request->pType == 'unit') {
            TblWebsiteAmenities::where('unit_id', $id)->delete();
        } else {
            TblWebsiteAmenities::where('multi_unit_id', $id)->delete();
        }

        foreach ($amenities as $key => $objAmenities) {
            if (!isset($objAmenities['isChecked'])) continue;

            $SQL = new TblWebsiteAmenities();

            if ($request->pType == 'unit') {
                $SQL->unit_id = $id;
                $SQL->pType = 'unit';
            } elseif ($request->pType == 'multiunit') {
                $SQL->multi_unit_id = $id;
                $SQL->pType = 'multiunit';
            } else {
                $SQL->home_id = $id;
                $SQL->pType = 'standalone';
            }

            $SQL->amenities_id = $objAmenities['amenities_id'];
            $SQL->amenities_name = $objAmenities['amenities_name'];
            $SQL->amenities_number = $objAmenities['amenities_number'] ?? 0;
            $SQL->position = $objAmenities['position'] ?? $key;
            $SQL->add_ip = $request->ip();
            $SQL->add_by = Auth::guard('admin')->user()->name;
            $SQL->save();
        }
        return redirect()->back()->with('success', 'Website Amenities saved successfully!');
    }

    //--------------additional charges---------------------//
    public function additionalCharges(Request $request, $id = null)
    {
        $detail = ($request->pType == 'unit') ? TblHomeUnit::find($id) : TblHomeMultiUnit::find($id);
        $parentHome = TblHome::where('id', $detail->home_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);
        if ($request->pType == 'unit') {
            $additionalCharges = HomeAdditionalCharge::where('unit_id', $id)->get();
        } else {
            $additionalCharges = HomeAdditionalCharge::where('multi_unit_id', $id)->get();
        }
        $charges = [];
        if ($additionalCharges->count() > 0) {
            foreach ($additionalCharges as $additionalCharge) {
                $charges[] = ['name' => $additionalCharge->name, 'price' => $additionalCharge->price, 'type' => $additionalCharge->type_option, 'display' => $additionalCharge->display_on_website];
            }
        } else {
            $charges = [
                ['name' => '', 'price' => '', 'type' => 'type_option', 'display' => false]
            ];
        }
        return view('pms.property.additional-charges', compact('charges', 'id', 'parentHome', 'id', 'detail'));
    }

    //----------------Save additional charges-------------//
    public function saveAdditionalCharge(Request $request)
    {

        $request->validate([
            'property_id' => 'required|integer',
            'id' => 'required|integer',
            'pType' => 'required|in:unit,multiunit',
            'charges' => 'array',
            'charges.*.name' => 'required|string|max:255',
            'charges.*.price' => 'required|numeric|min:0',

        ]);
        $unitId = $request->id;
        $pType = $request->pType;
        $charges = $request->charges;
        if ($pType === 'unit') {
            HomeAdditionalCharge::where('unit_id', $unitId)->forceDelete();
        } else {
            HomeAdditionalCharge::where('multi_unit_id', $unitId)->forceDelete();
        }

        foreach ($charges as $charge) {
            $detail = [
                'name' => $charge['name'],
                'price' => $charge['price'],
                'type_option' => $charge['type'],
                'display_on_website' => isset($charge['display']) && $charge['display'] == 'on' ? 1 : 0,
                'unit_id' => $pType === 'unit' ? $unitId : null,
                'multi_unit_id' => $pType === 'multiunit' ? $unitId : null,
                'pType' => $pType,
            ];
            HomeAdditionalCharge::create($detail);
        }
        return redirect()->route('pms.property.unit.or.multiunit.additionalcharges', ['id' => $request->id, 'property_id' => $request->property_id, 'pType' => $request->pType])->with('success', 'Property additional charges saved successfully.');
    }


    //--------------fetch gallery---------------//
    public function gallery(Request $request, $id = null)
    {
        ini_set('memory_limit', '50000M');
        $detail = ($request->pType == 'unit') ? TblHomeUnit::find($id) : TblHomeMultiUnit::find($id);
        $parentHome = TblHome::where('id', $detail->home_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);
        $columns = [
            'id',
            'home_id',
            'unit_id',
            'multi_unit_id',
            'type',
            'title',
            'filename',
            'default',
            'position',
            'status',
            'add_ip',
            'add_by',
            'update_ip',
            'update_by',
            'created_at',
            'updated_at',
            'deleted_at',
            'tbl_ru_image_type_id',
            'pType'
        ];
        if ($request->pType == 'unit') {
            $existingImages = TblHomeImageVideo::where('unit_id', $id)->where('type', 'image')->orderBy('position', 'asc')->get($columns);
        } else {
            $existingImages = TblHomeImageVideo::where('multi_unit_id', $id)->where('type', 'image')->orderBy('position', 'asc')->get($columns);
        }
        $existingImages = $existingImages->map(function ($img) {
            $img->img_url  =  asset($img->filename);
            return $img;
        });
        $options = TblRuImageType::where('status', 2)->orderBy('image_category_name')->get()->pluck('image_category_name')->toArray();
        return view('pms.property.gallery', compact('detail', 'existingImages', 'id', 'parentHome', 'options'));
    }


    //--------------save gallery-----------------//
    public function gallerySave(Request $request)
    {

        ini_set('upload_max_filesize', '51233333M');
        ini_set('post_max_size', '5123333M');
        ini_set('memory_limit', '5123333M');
        ini_set('max_execution_time', '3000000000000000');
        ini_set('max_input_time', '3000000000000000');
        set_time_limit(3000000000000000);


        if ($request->missing('image_categories')) {
            return redirect()->route('pms.property.unit.or.multiunit.gallery', [
                'id' => $request->id,
                'property_id' => $request->property_id,
                'pType' => $request->pType,
            ])->with('error', 'Please upload atleast 1 imagge.');
        }


        $new_items = [];
        foreach ($request->input('image_categories', [])  as $key => $value) {
            if (is_string($key) && str_starts_with($key, 'new-')) {
                $new_items[] = $value;
            }
        }

        $imageCategories = $request->input('image_categories', []);
        $base64Images = $request->input('new_images', []);
        // Handle deletions
        if ($request->has('deleted_image_ids')) {
            foreach ($request->deleted_image_ids as $id) {
                $image = TblHomeImageVideo::find($id);
                if ($image) {
                    $filePath = 'public/home/' . $image->filename;
                    $smallFilePath = 'public/home/small/' . $image->filename;
                    $mediumFilePath = 'public/home/medium/' . $image->filename;
                    $imagesFilePath = 'public/home/images/' . $image->filename;
                    Storage::delete([$filePath, $smallFilePath, $mediumFilePath, $imagesFilePath]);
                    $image->delete();
                }
            }
        }
        // Update positions and categories for existing images
        if ($request->has('positions')) {
            foreach ($request->positions as $id => $position) {
                $image = TblHomeImageVideo::find($id);
                if ($image) {
                    $category = $imageCategories[$id] ?? $image->title;
                    $ruImage = TblRuImageType::where('image_category_name', $category)->first();
                    $image->update([
                        'position' => $position,
                        'title' => $category,
                        'tbl_ru_image_type_id' => $ruImage ? $ruImage->ru_image_type_id : $image->tbl_ru_image_type_id,
                    ]);
                }
            }
        }
        // Save new images
        $maxPosition = TblHomeImageVideo::where(function ($query) use ($request) {
            if ($request->pType == 'unit') {
                $query->where('type', 'image')->where('unit_id', $request->id);
            } elseif ($request->pType == 'multiunit') {
                $query->where('type', 'image')->where('multi_unit_id', $request->id);
            }
        })->max('position') ?? 0;

        foreach ($base64Images as $index => $base64) {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                $imageData = substr($base64, strpos($base64, ',') + 1);
                $imageData = base64_decode($imageData);
                $extension = strtolower($type[1]);
                $fileName = Str::uuid()->toString() . '.' . $extension;
                $path = 'public/home/images/' . $fileName;
                Storage::put($path, $imageData);

                $newDirectory = 'public/home/small/';
                $newmediumDirectory = 'public/home/medium/';
                $storingPathImages = 'public/home/images/' . $fileName;
                //$this->resizeAndOptimizeImage($path, $storingPathImages);
              //  Storage::copy($path, $storingPathImages);
              //  $this->resizeAndOptimizeImage($path, $newDirectory . $fileName);
               // $this->resizeAndOptimizeImageMedium($path, $newmediumDirectory . $fileName);

                if ($extension !== 'avif') {
                    $this->resizeAndOptimizeImage($path, $newDirectory . $fileName);
                    $this->resizeAndOptimizeImageMedium($path, $newmediumDirectory . $fileName);
                }
                else {
                    Storage::copy($path, $newDirectory . $fileName);
                    Storage::copy($path, $newmediumDirectory . $fileName);
                }



                $categoryKey = array_keys($new_items)[$index] ?? null;
                $category = $new_items[$categoryKey];
                $ruImage = TblRuImageType::where('image_category_name', $category)->first();

                $detail = new TblHomeImageVideo();
                if ($request->pType == 'unit') {
                    $detail->unit_id = $request->id;
                } elseif ($request->pType == 'multiunit') {
                    $detail->multi_unit_id = $request->id;
                }
                $detail->pType = $request->pType;
                $detail->type = 'image';
                $detail->tbl_ru_image_type_id = $ruImage ? $ruImage->ru_image_type_id : null;
                $detail->filename = $fileName;
                $detail->default = 0;
                $detail->title = $category;
                $detail->position = $maxPosition + $index + 1;
                $detail->home_id = $request->input('property_id');
                $detail->add_ip = $request->ip();
                $detail->add_by = 'Admin';
                //$detail->base64_image = $base64;
                $detail->save();
            }
        }

        $detail = ($request->pType == 'unit') ? TblHomeUnit::find($request->id) : TblHomeMultiUnit::find($request->id);

        if ($detail->ru_property_id) {

            $ruResponse = $this->otaService->updateUnitImagesInRu($detail->id);
        }

        return redirect()->route('pms.property.unit.or.multiunit.gallery', [
            'id' => $request->id,
            'property_id' => $request->property_id,
            'pType' => $request->pType,
        ])->with('success', 'Property images saved successfully.');
    }



    private function resizeAndOptimizeImage($oldpath, $storingPath)
    {
        $imageContent = Storage::get($oldpath);
        $image = imagecreatefromstring($imageContent);
        if (!$image) {
            throw new \Exception('Failed to load image');
        }
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);
        $resizeWidth = 600;
        $resizeHeight = ($resizeWidth / $imageWidth) * $imageHeight;
        $resizedImage = imagecreatetruecolor($resizeWidth, $resizeHeight);
        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $imageWidth, $imageHeight);
        $dir = dirname($storingPath);
        if (!Storage::exists($dir)) {
            Storage::makeDirectory($dir, 0777, true);
        }
        imagejpeg($resizedImage, Storage::path($storingPath), 85);
        imagedestroy($image);
        imagedestroy($resizedImage);
    }

    private function resizeAndOptimizeImageMedium($oldpath, $storingPath)
    {
        $imageContent = Storage::get($oldpath);
        $image = imagecreatefromstring($imageContent);
        if (!$image) {
            throw new \Exception('Failed to load image');
        }
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);
        $resizeWidth = 1000;
        $resizeHeight = ($resizeWidth / $imageWidth) * $imageHeight;
        $resizedImage = imagecreatetruecolor($resizeWidth, $resizeHeight);
        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $imageWidth, $imageHeight);
        $dir = dirname($storingPath);
        if (!Storage::exists($dir)) {
            Storage::makeDirectory($dir, 0777, true);
        }
        imagejpeg($resizedImage, Storage::path($storingPath), 85);
        imagedestroy($image);
        imagedestroy($resizedImage);
    }



    public function layoutImages(Request $request, $id = null)
    {
        ini_set('memory_limit', '50000M');
        $detail = ($request->pType == 'unit') ? TblHomeUnit::find($id) : TblHomeMultiUnit::find($id);
        $parentHome = TblHome::where('id', $detail->home_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);
        if ($request->pType == 'unit') {
            $existingImages = TbllayoutImage::where('unit_id', $id)->orderBy('position', 'asc')->get();
        } else {
            $existingImages = TbllayoutImage::where('multi_unit_id', $id)->orderBy('position', 'asc')->get();
        }
        //dd($existingImages);
        $options = TblRuImageType::where('status', 2)->orderBy('image_category_name')->get()->pluck('image_category_name')->toArray();
        return view('pms.property.layout-Image', compact('detail', 'existingImages', 'id', 'parentHome', 'options'));
    }


    public function SavelayoutImages(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        // Handle deletions
        if ($request->has('deleted_image_ids')) {
            foreach ($request->deleted_image_ids as $id) {
                $image = TbllayoutImage::find($id);
                if ($image) {
                    Storage::delete('public/layoutImages/' . $image->filename);
                    $image->delete();
                }
            }
        }

        // Update positions for existing images
        if ($request->has('positions')) {
            foreach ($request->positions as $id => $position) {
                TbllayoutImage::where('id', $id)->update(['position' => $position]);
            }
        }

        // Save new images
        $maxPosition = TbllayoutImage::where(function ($query) use ($request) {
            if ($request->pType == 'unit') {
                $query->where('type', 'image')->where('unit_id', $request->id);
            } elseif ($request->pType == 'multiunit') {
                $query->where('type', 'image')->where('multi_unit_id', $request->id);
            }
        })->max('position') ?? 0;

        $base64Images = $request->input('new_images', []);

        foreach ($base64Images as $index => $base64) {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                $imageData = base64_decode(substr($base64, strpos($base64, ',') + 1));
                $extension = strtolower($type[1]);
                $fileName = Str::uuid() . '.' . $extension;
                $path = 'public/layoutImages/' . $fileName;

                Storage::put($path, $imageData);

                $detail = new TbllayoutImage();
                if ($request->pType == 'unit') {
                    $detail->unit_id = $request->id;
                } elseif ($request->pType == 'multiunit') {
                    $detail->multi_unit_id = $request->id;
                }

                $detail->pType = $request->pType;
                $detail->type = 'image';
                $detail->filename = $fileName;
                $detail->default = 0;
                $detail->position = $maxPosition + $index + 1;
                $detail->home_id = $request->property_id;
                $detail->save();
            }
        }

        return redirect()->route('pms.property.unit.or.multiunit.layoutimages', [
            'id' => $request->id,
            'property_id' => $request->property_id,
            'pType' => $request->pType,
        ])->with('success', 'Layout images saved successfully.');
    }




    // Show gallery video page
    public function galleryVideo(Request $request, $id = null)
    {
        $detail = ($request->pType == 'unit') ? TblHomeUnit::find($id) : TblHomeMultiUnit::find($id);
        $parentHome = TblHome::where('id', $detail->home_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);

        $existingVideos = TblHomeImageVideo::where(function ($query) use ($request, $id) {
            if ($request->pType == 'unit') {
                $query->where('unit_id', $id);
            } else {
                $query->where('multi_unit_id', $id);
            }
        })->where('type', 'video')->orderBy('position', 'asc')->first();

        $options = TblRuImageType::where('status', 2)->pluck('image_category_name')->toArray();
        return view('pms.property.video', compact('detail', 'existingVideos', 'id', 'parentHome', 'options'));
    }

    public function gallerySaveVideo(Request $request)
    {

        $request->validate([
            'video_file' => 'required|file|mimes:mp4|max:204800', // 200MB in KB
            'property_id' => 'required|integer',
            'id' => 'required|integer',
            'pType' => 'required|in:unit,multiunit',
        ]);
        $file = $request->file('video_file');
        if (!$file) {
            return back()->with('error', 'Please upload a valid video.');
        }

        $extension = $file->getClientOriginalExtension();
        $fileName = Str::uuid()->toString() . '.' . $extension;
        $path = 'public/home/video/' . $fileName;

        $existingVideo = TblHomeImageVideo::where('id', $request->tbl_image_video_id)->where(function ($query) use ($request) {
            if ($request->pType == 'unit') {
                $query->where('unit_id', $request->id);
            } else {
                $query->where('multi_unit_id', $request->id);
            }
        })->where('type', 'video')->first();

        try {
            Storage::put($path, file_get_contents($file));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to save video: ' . $e->getMessage());
        }

        try {
            if ($existingVideo) {
                $oldPath = 'public/home/video/' . $existingVideo->filename;
                if (Storage::exists($oldPath)) {
                    Storage::delete($oldPath);
                }
                $existingVideo->filename = $fileName;
                $existingVideo->title = 'Video';
                $existingVideo->add_by = 'Admin';
                $existingVideo->add_ip = $request->ip();
                $existingVideo->base64_image = null;
                $existingVideo->updated_at = now();
                $existingVideo->save();
            } else {
                $video = new TblHomeImageVideo();
                $video->type = 'video';
                $video->pType = $request->pType;
                $video->filename = $fileName;
                $video->default = 0;
                $video->title = 'Video';
                $video->position = 1;
                $video->tbl_ru_image_type_id = null;
                $video->home_id = $request->property_id;
                $video->add_by = 'Admin';
                $video->add_ip = $request->ip();
                $video->base64_image = null;
                if ($request->pType == 'unit') {
                    $video->unit_id = $request->id;
                } else {
                    $video->multi_unit_id = $request->id;
                }
                $video->save();
            }
        } catch (\Exception $e) {
            Storage::delete($path);
            return back()->with('error', 'Failed to save video record: ' . $e->getMessage());
        }

        return redirect()->route('pms.property.unit.or.multiunit.video', [
            'id' => $request->id,
            'property_id' => $request->property_id,
            'pType' => $request->pType,
        ])->with('success', 'Property video saved successfully.');
    }



    public function galleryDeleteVideo(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'pType' => 'required|in:unit,multiunit',
        ]);

        try {
            // Record find karo based on ID + pType
            $video = TblHomeImageVideo::where('id', $request->id)
                ->where('pType', $request->pType)
                ->first();

            if (!$video) {
                return response()->json([
                    'status' => false,
                    'message' => 'Video not found for this property type.',
                ], 404);
            }

            // File ka path
            $videoPath = 'public/home/video/' . $video->filename;

            // Agar file exist karti hai to delete kar do
            if (Storage::exists($videoPath)) {
                Storage::delete($videoPath);
            }

            // Database se delete kar do
            TblHomeImageVideo::where('id', $request->id)
                ->where('pType', $request->pType)
                ->delete();

            return response()->json([
                'status' => true,
                'message' => 'Video deleted successfully.',
                'deleted_id' => $request->id,
                'deleted_pType' => $request->pType
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //--------------fetch commas---------------//
    public function commas(Request $request, $id = null)
    {
        $detail = ($request->pType == 'unit') ? TblHomeUnit::find($id) : TblHomeMultiUnit::find($id);
        $parentHome = TblHome::where('id', $detail->home_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);
        $comms = [];
        if ($detail) {
            if ($detail->comms) {
                $comms = json_decode($detail->comms);
            }
        }
        return view('pms.property.commas', compact('detail', 'parentHome', 'id', 'comms'));
    }

    //----------------save comms-------------//
    public function commasSave(Request $request, $id = null)
    {
        $detail = ($request->pType == 'unit')
            ? TblHomeUnit::find($request->id)
            : TblHomeMultiUnit::find($request->id);

        $data = $request->all();

        $response = [
            'name'  => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'phones' => [],
            'cc_emails' => [],
        ];
        if (!empty($data['phones']) && is_array($data['phones'])) {
            $response['phones'] = array_values(
                array_unique(
                    array_filter($data['phones'])
                )
            );
        }
        if (!empty($data['ccEmails']) && is_array($data['ccEmails'])) {
            $response['cc_emails'] = array_values(
                array_unique(
                    array_filter($data['ccEmails'])
                )
            );
        }

        $detail->comms = json_encode($response);
        $detail->save();

        return redirect()->route(
            'pms.property.unit.or.multiunit.commas',
            [
                'id' => $request->id,
                'property_id' => $request->property_id,
                'pType' => $request->pType,
            ]
        )->with('success', 'Comms saved successfully.');
    }

    public function cancellationSlab(Request $request, $id = null)
    {
        $detail = ($request->pType == 'unit') ? TblHomeUnit::find($id) : TblHomeMultiUnit::find($id);
        $states = TblState::where('status', 1)->get();
        $homeTypes = TblHomeType::where('status', 1)->get();
        $locations = [];
        if ($detail && $detail->state_id) {
            $locations = TblLocation::where('state_id', $detail->state_id)->where('status', 1)->get();
        }
        $detail = ($request->pType == 'unit') ? TblHomeUnit::find($id) : TblHomeMultiUnit::find($id);
        $cancellation_slab = ($request->pType == 'unit') ? CancellationSlab::where('unit_id', $id)->get() : CancellationSlab::where('multi_unit_id', $id)->get();

        $parentHome = TblHome::where('id', $detail->home_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);
        return view('pms.property.cancellation-slab', compact('detail', 'states', 'homeTypes', 'locations', 'id', 'cancellation_slab', 'parentHome'));
    }

    public function cancellationSlabSave(Request $request)
    {
        try {
            if ($request->pType == 'unit') {
                CancellationSlab::where(['unit_id' => $request->id])->forceDelete();
                foreach ($request->slab as $key => $value) {
                    $obj = new CancellationSlab();
                    $obj->home_id = $request->property_id;
                    $obj->unit_id = $request->id;
                    $obj->slab_from = $value['slab_from'];
                    $obj->slab_to = $value['slab_to'];
                    $obj->slab = $value['slab'];
                    $obj->pType = 'unit';
                    $obj->save();
                }
            } else {
                CancellationSlab::where(['multi_unit_id' => $request->id])->forceDelete();
                foreach ($request->slab as $key => $value) {
                    $obj = new CancellationSlab();
                    $obj->home_id = $request->property_id;
                    $obj->multi_unit_id = $request->id;
                    $obj->slab_from = $value['slab_from'];
                    $obj->slab_to = $value['slab_to'];
                    $obj->slab = $value['slab'];
                    $obj->pType = 'multiunit';
                    $obj->save();
                }
            }
            return redirect()->route('pms.property.unit.or.multiunit.cancellationslab', ['id' => $request->id, 'property_id' => $request->property_id, 'pType' => $request->pType,])->with('success', 'Cancellation Slab Saved Successfully.');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function floor(Request $request, $id = null)
    {

        $detail = ($request->pType == 'unit') ? TblHomeUnit::find($id) : TblHomeMultiUnit::find($id);
        $bedRoomAmenityList = DB::table('tbl_ru_amenities')->where('amenities_type', 'Bedroom')->get();
        $bathRoomAmenityList = DB::table('tbl_ru_amenities')->where('amenities_type', 'Bathroom')->get();
        $ruFloorList = DB::table('tbl_ru_floor')->get();

        $floorSelectedDetail = TblRuAmenityMapping::where('unit_id', $id)->groupBy('floor_id')->first('floor_id');

        // Bedroom
        $bedRoomSelectedAmenityBedRooms = TblRuAmenityMapping::where('unit_id', $id)->where('amenity_type', 'Bedroom')->groupBy('bedroom_no')->get('bedroom_no');
        $bedRoomSelectedAmenityList = [];
        foreach ($bedRoomSelectedAmenityBedRooms as $keybeb => $bedRoomSelectedAmenityBedRoom) {
            $bedRoomSelectedAmenityDetail = TblRuAmenityMapping::where('unit_id', $id)->where('bedroom_no', $keybeb + 1)->where('amenity_type', 'Bedroom')->pluck('ru_amenity_id')->toArray();
            array_push($bedRoomSelectedAmenityList, $bedRoomSelectedAmenityDetail);
        }

        // Bathroom
        $bathRoomSelectedAmenityBedRooms = TblRuAmenityMapping::where('unit_id', $id)->where('amenity_type', 'Bathroom')->groupBy('bathroom_no')->get('bathroom_no');
        $bathRoomSelectedAmenityList = [];
        foreach ($bathRoomSelectedAmenityBedRooms as $keybeb => $bathRoomSelectedAmenityBedRoom) {
            $bathRoomSelectedAmenityDetail = TblRuAmenityMapping::where('unit_id', $id)->where('bathroom_no', $keybeb + 1)->where('amenity_type', 'Bathroom')->pluck('ru_amenity_id')->toArray();
            array_push($bathRoomSelectedAmenityList, $bathRoomSelectedAmenityDetail);
        }

        $roomSpecificAmmenities = DB::table('tbl_ru_specific_rooms')->whereNotIn('ru_room_id', [81, 257])->get();
        foreach ($roomSpecificAmmenities as $key => $value) {
            $roomSpecificAmmenities[$key]->ammenities = DB::table('tbl_ru_specific_room_ammenities')->where('tbl_ru_specific_room_ammenity_id', $value->ru_room_id)->get();
        }

        $roomAmmenities = DB::table('tbl_ru_specific_room_ammenities')->get();

        if ($request->pType == 'unit') {
            $wcAmmenityListModel = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('unit_id', $id)->where('ru_room_id', '53')->get()->pluck('ammenity_id')->toArray();
            $kitchenAndLivingRoomAmmenitiesModel = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('unit_id', $id)->where('ru_room_id', '94')->get()->pluck('ammenity_id')->toArray();
            $kitchenRoomAmmenitiesModel = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('unit_id', $id)->where('ru_room_id', '101')->get()->pluck('ammenity_id')->toArray();
            $livingRoomAmmenitiesModel = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('unit_id', $id)->where('ru_room_id', '249')->get()->pluck('ammenity_id')->toArray();
            $livingBedRoomAmmenitiesModel = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('unit_id', $id)->where('ru_room_id', '372')->get()->pluck('ammenity_id')->toArray();
            $livingBedRoomKitchenAmmenitiesModel = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('unit_id', $id)->where('ru_room_id', '517')->get()->pluck('ammenity_id')->toArray();
        } else {
            $wcAmmenityListModel = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('multi_unit_id', $id)->where('ru_room_id', '53')->get()->pluck('ammenity_id')->toArray();
            $kitchenAndLivingRoomAmmenitiesModel = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('multi_unit_id', $id)->where('ru_room_id', '94')->get()->pluck('ammenity_id')->toArray();
            $kitchenRoomAmmenitiesModel = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('multi_unit_id', $id)->where('ru_room_id', '101')->get()->pluck('ammenity_id')->toArray();
            $livingRoomAmmenitiesModel = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('multi_unit_id', $id)->where('ru_room_id', '249')->get()->pluck('ammenity_id')->toArray();
            $livingBedRoomAmmenitiesModel = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('multi_unit_id', $id)->where('ru_room_id', '372')->get()->pluck('ammenity_id')->toArray();
            $livingBedRoomKitchenAmmenitiesModel = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('multi_unit_id', $id)->where('ru_room_id', '517')->get()->pluck('ammenity_id')->toArray();
        }

        // Fetch homeEdit data (from Vue props)
        $homeEdit = ($request->pType == 'unit') ? DB::table('tbl_home_units')->find($id) : DB::table('tbl_home_multi_units')->find($id);

        // Prepare bedroomCount (matching Vue logic)
        $bedroomCount = [];
        if (count($bedRoomSelectedAmenityList) > 0) {
            if (count($bedRoomSelectedAmenityList) < $homeEdit->no_of_bedrooms) {
                foreach ($bedRoomSelectedAmenityList as $idx => $item) {
                    if ($idx + 1 <= $homeEdit->no_of_bedrooms) {
                        $bedroomCount[] = ['bedRoomSelectedAmenities' => $item];
                    }
                }
                $extraBedCount = $homeEdit->no_of_bedrooms - count($bedRoomSelectedAmenityList);
                for ($i = 0; $i < $extraBedCount; $i++) {
                    $bedroomCount[] = ['bedRoomSelectedAmenities' => []];
                }
            } else {
                for ($i = 0; $i < $homeEdit->no_of_bedrooms; $i++) {
                    $bedroomCount[] = ['bedRoomSelectedAmenities' => $bedRoomSelectedAmenityList[$i]];
                }
            }
        } else {
            for ($i = 0; $i < $homeEdit->no_of_bedrooms; $i++) {
                $bedroomCount[] = ['bedRoomSelectedAmenities' => []];
            }
        }

        // Prepare bathroomCount (matching Vue logic)
        $bathroomCount = [];
        if (count($bathRoomSelectedAmenityList) > 0) {
            if (count($bathRoomSelectedAmenityList) < $homeEdit->no_of_bathrooms) {
                foreach ($bathRoomSelectedAmenityList as $item) {
                    $bathroomCount[] = ['bathRoomSelectedAmenities' => $item];
                }
                $extraBathCount = $homeEdit->no_of_bathrooms - count($bathRoomSelectedAmenityList);
                for ($i = 0; $i < $extraBathCount; $i++) {
                    $bathroomCount[] = ['bathRoomSelectedAmenities' => []];
                }
            } else {
                for ($i = 0; $i < $homeEdit->no_of_bathrooms; $i++) {
                    $bathroomCount[] = ['bathRoomSelectedAmenities' => $bathRoomSelectedAmenityList[$i]];
                }
            }
        } else {
            for ($i = 0; $i < $homeEdit->no_of_bathrooms; $i++) {
                $bathroomCount[] = ['bathRoomSelectedAmenities' => []];
            }
        }

        // Prepare specific amenities lists (matching Vue onMounted logic)
        $wcAmmenityList = [];
        $kitchenAndLivingRoomAmmenities = [];
        $kitchenRoomAmmenities = [];
        $livingRoomAmmenities = [];
        $livingBedRoomAmmenities = [];
        $livingBedRoomKitchenAmmenities = [];

        foreach ($roomAmmenities as $item) {
            $itemDetail = [
                'tbl_ru_specific_room_ammenity_id' => $item->ru_room_ammenity_id,
                'ru_room_ammenity_name' => $item->ru_room_ammenity_name,
            ];
            if ($item->tbl_ru_specific_room_ammenity_id == '53') {
                $wcAmmenityList[] = $itemDetail;
            }
            if ($item->tbl_ru_specific_room_ammenity_id == '94') {
                $kitchenAndLivingRoomAmmenities[] = $itemDetail;
            }
            if ($item->tbl_ru_specific_room_ammenity_id == '101') {
                $kitchenRoomAmmenities[] = $itemDetail;
            }
            if ($item->tbl_ru_specific_room_ammenity_id == '249') {
                $livingRoomAmmenities[] = $itemDetail;
            }
            if ($item->tbl_ru_specific_room_ammenity_id == '372') {
                $livingBedRoomAmmenities[] = $itemDetail;
            }
            if ($item->tbl_ru_specific_room_ammenity_id == '517') {
                $livingBedRoomKitchenAmmenities[] = $itemDetail;
            }
        }

        $parentHome = TblHome::where('id', $detail->home_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);

        return view('pms.property.floor', compact(
            'bedRoomAmenityList',
            'bathRoomAmenityList',
            'ruFloorList',
            'floorSelectedDetail',
            'bedRoomSelectedAmenityList',
            'bathRoomSelectedAmenityList',
            'roomSpecificAmmenities',
            'roomAmmenities',
            'wcAmmenityListModel',
            'kitchenAndLivingRoomAmmenitiesModel',
            'kitchenRoomAmmenitiesModel',
            'livingRoomAmmenitiesModel',
            'livingBedRoomAmmenitiesModel',
            'livingBedRoomKitchenAmmenitiesModel',
            'bedroomCount',
            'bathroomCount',
            'wcAmmenityList',
            'kitchenAndLivingRoomAmmenities',
            'kitchenRoomAmmenities',
            'livingRoomAmmenities',
            'livingBedRoomAmmenities',
            'livingBedRoomKitchenAmmenities',
            'homeEdit',
            'id',
            'detail',
            'parentHome'
        ));
    }


    public function floorSave(Request $request)
    {
        if ($request->floor == '') {
            return redirect()->route('pms.property.unit.or.multiunit.floor', ['id' => $request->id, 'property_id' => $request->property_id, 'pType' => $request->pType])->with('error', 'Please select floor.');
        }

        if (
            empty($request->bedroomAmmenity) &&
            empty($request->bathroomAmmenity) &&
            empty($request->wcAmmenities) &&
            empty($request->kitchenAndLivingRoomAmmenities) &&
            empty($request->kitchenRoomAmmenities) &&
            empty($request->livingRoomAmmenities) &&
            empty($request->livingBedRoomAmmenities) &&
            empty($request->livingBedRoomKitchenAmmenities)
        ) {
            return redirect()->route('pms.property.unit.or.multiunit.floor', ['id' => $request->id, 'property_id' => $request->property_id, 'pType' => $request->pType])->with('error', 'Please select atleast 1 amenity.');
        }
        $id = $request->id;
        if (isset($request->pType)) {
            if ($request->pType == 'unit') {
                TblRuAmenityMapping::where('unit_id', $id)->forceDelete();
            } elseif ($request->pType == 'multiunit') {
                TblRuAmenityMapping::where('multi_unit_id', $id)->forceDelete();
            }
        } else {
            TblRuAmenityMapping::where('home_id', $id)->forceDelete();
        }

        if (isset($request->bedroomAmmenity)) {
            foreach ($request->bedroomAmmenity as $key => $bedRoomSelectedAmenity) {
                $bedroomKey = $key + 1;
                foreach ($bedRoomSelectedAmenity as $value) {
                    $bedroomAmenityDetail = new TblRuAmenityMapping();
                    if (isset($request->pType)) {
                        if ($request->pType == 'unit') {
                            $bedroomAmenityDetail->unit_id = $id;
                            $bedroomAmenityDetail->pType = 'unit';
                        } elseif ($request->pType == 'multiunit') {
                            $bedroomAmenityDetail->multi_unit_id = $id;
                            $bedroomAmenityDetail->pType = 'multiunit';
                        }
                    } else {
                        $bedroomAmenityDetail->home_id = $id;
                        $bedroomAmenityDetail->pType = 'standalone';
                    }
                    $bedroomAmenityDetail->floor_id = $request->floor;
                    $bedroomAmenityDetail->bedroom_no = $bedroomKey;
                    $bedroomAmenityDetail->amenity_type = 'Bedroom';
                    $bedroomAmenityDetail->ru_amenity_id = $value;
                    $bedroomAmenityDetail->save();
                }
            }
        }

        if (isset($request->bathroomAmmenity)) {
            foreach ($request->bathroomAmmenity as $bkey => $bathRoomSelectedAmenity) {
                $bathroomKey = $bkey + 1;
                foreach ($bathRoomSelectedAmenity as $value) {
                    $bathroomAmenityDetail = new TblRuAmenityMapping();
                    if (isset($request->pType)) {
                        if ($request->pType == 'unit') {
                            $bathroomAmenityDetail->unit_id = $id;
                            $bathroomAmenityDetail->pType = 'unit';
                        } elseif ($request->pType == 'multiunit') {
                            $bathroomAmenityDetail->multi_unit_id = $id;
                            $bathroomAmenityDetail->pType = 'multiunit';
                        }
                    } else {
                        $bathroomAmenityDetail->home_id = $id;
                        $bathroomAmenityDetail->pType = 'standalone';
                    }
                    $bathroomAmenityDetail->floor_id = $request->floor;
                    $bathroomAmenityDetail->bathroom_no = $bathroomKey;
                    $bathroomAmenityDetail->amenity_type = 'Bathroom';
                    $bathroomAmenityDetail->ru_amenity_id = $value;
                    $bathroomAmenityDetail->save();
                }
            }
        }

        if (isset($request->pType)) {
            if ($request->pType == 'unit') {
                DB::table('tbl_ru_specific_room_ammenity_mappings')->where('unit_id', $id)->Delete();
            } else {
                DB::table('tbl_ru_specific_room_ammenity_mappings')->where('multi_unit_id', $id)->Delete();
            }
        }

        if (isset($request->wcAmmenities)) {
            foreach ($request->wcAmmenities as $val) {
                if ($request->pType == 'unit') {
                    $detail = array(
                        'ammenity_id' => $val,
                        'ru_room_id' => 53,
                        'unit_id' => $id,
                        'pType' => $request->pType,
                        'created_at' => now(),
                        'updated_at' => now(),
                    );
                } else {
                    $detail = array(
                        'ammenity_id' => $val,
                        'ru_room_id' => 53,
                        'multi_unit_id' => $id,
                        'pType' => $request->pType,
                        'created_at' => now(),
                        'updated_at' => now(),
                    );
                }
                DB::table('tbl_ru_specific_room_ammenity_mappings')->insert($detail);
            }
        }

        if (isset($request->kitchenAndLivingRoomAmmenities)) {
            foreach ($request->kitchenAndLivingRoomAmmenities as $val) {
                if ($request->pType == 'unit') {
                    $detail = array(
                        'ammenity_id' => $val,
                        'ru_room_id' => 94,
                        'unit_id' => $id,
                        'pType' => $request->pType,
                        'created_at' => now(),
                        'updated_at' => now(),
                    );
                } else {
                    $detail = array(
                        'ammenity_id' => $val,
                        'ru_room_id' => 94,
                        'multi_unit_id' => $id,
                        'pType' => $request->pType,
                        'created_at' => now(),
                        'updated_at' => now(),
                    );
                }
                DB::table('tbl_ru_specific_room_ammenity_mappings')->insert($detail);
            }
        }

        if (isset($request->kitchenRoomAmmenities)) {
            foreach ($request->kitchenRoomAmmenities as $val) {
                if ($request->pType == 'unit') {
                    $detail = array(
                        'ammenity_id' => $val,
                        'ru_room_id' => 101,
                        'unit_id' => $id,
                        'pType' => $request->pType,
                        'created_at' => now(),
                        'updated_at' => now(),
                    );
                } else {
                    $detail = array(
                        'ammenity_id' => $val,
                        'ru_room_id' => 101,
                        'multi_unit_id' => $id,
                        'pType' => $request->pType,
                        'created_at' => now(),
                        'updated_at' => now(),
                    );
                }
                DB::table('tbl_ru_specific_room_ammenity_mappings')->insert($detail);
            }
        }

        if (isset($request->livingRoomAmmenities)) {
            foreach ($request->livingRoomAmmenities as $val) {
                if ($request->pType == 'unit') {
                    $detail = array(
                        'ammenity_id' => $val,
                        'ru_room_id' => 249,
                        'unit_id' => $id,
                        'pType' => $request->pType,
                        'created_at' => now(),
                        'updated_at' => now(),
                    );
                } else {
                    $detail = array(
                        'ammenity_id' => $val,
                        'ru_room_id' => 249,
                        'multi_unit_id' => $id,
                        'pType' => $request->pType,
                        'created_at' => now(),
                        'updated_at' => now(),
                    );
                }
                DB::table('tbl_ru_specific_room_ammenity_mappings')->insert($detail);
            }
        }

        if (isset($request->livingBedRoomAmmenities)) {
            foreach ($request->livingBedRoomAmmenities as $val) {
                if ($request->pType == 'unit') {
                    $detail = array(
                        'ammenity_id' => $val,
                        'ru_room_id' => 372,
                        'unit_id' => $id,
                        'pType' => $request->pType,
                        'created_at' => now(),
                        'updated_at' => now(),
                    );
                } else {
                    $detail = array(
                        'ammenity_id' => $val,
                        'ru_room_id' => 372,
                        'multi_unit_id' => $id,
                        'pType' => $request->pType,
                        'created_at' => now(),
                        'updated_at' => now(),
                    );
                }
                DB::table('tbl_ru_specific_room_ammenity_mappings')->insert($detail);
            }
        }

        if (isset($request->livingBedRoomKitchenAmmenities)) {
            foreach ($request->livingBedRoomKitchenAmmenities as $val) {
                if ($request->pType == 'unit') {
                    $detail = array(
                        'ammenity_id' => $val,
                        'ru_room_id' => 517,
                        'unit_id' => $id,
                        'pType' => $request->pType,
                        'created_at' => now(),
                        'updated_at' => now(),
                    );
                } else {
                    $detail = array(
                        'ammenity_id' => $val,
                        'ru_room_id' => 517,
                        'multi_unit_id' => $id,
                        'pType' => $request->pType,
                        'created_at' => now(),
                        'updated_at' => now(),
                    );
                }
                DB::table('tbl_ru_specific_room_ammenity_mappings')->insert($detail);
            }
        }

        return redirect()->route('pms.property.unit.or.multiunit.floor', ['id' => $request->id, 'property_id' => $request->property_id, 'pType' => $request->pType])->with('success', 'Specific Amenities Mapped Successfully.');
    }


    /* review */
    public function review(Request $request, $id = null)
    {
        $detail = ($request->pType == 'unit') ? TblHomeUnit::find($id) : TblHomeMultiUnit::find($id);
        if (!$detail) {
            return redirect()->back()->with('error', 'Property details not found for the given ID.');
        }
        $parentHome = TblHome::where('id', $detail->home_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);
        $reviews = TblHomeReview::with('reviewImages')->where(function ($query) use ($request, $id) {
            if ($request->pType === 'unit') {
                $query->where('unit_id', $id)->where('pType', 'unit');
            } elseif ($request->pType === 'multiunit') {
                $query->where('multi_unit_id', $id)->where('pType', 'multiunit');
            }
        })
            ->get();
        $firstReview = $reviews->first();
        //dd($firstReview);
        if ($request->review_id) {
            $reviewUpdate = TblHomeReview::where('id', $request->review_id)->first();
        } else {
            $reviewUpdate = null;
        }
        $channelData = TblReviewImages::where('status', 1)->get();
        //dd($reviewUpdate);
        return view('pms.property.review', compact('firstReview', 'channelData', 'reviews', 'reviewUpdate', 'id', 'detail', 'parentHome'));
    }


    public function reviewSave(Request $request, $id = null)
    {
        $request->validate([
            'guest_name' => 'required',
            'review_date' => 'required',
            'rating' => 'required',
            'comment' => 'required',
            'review_type' => 'required',
            'link' => 'required|url'
        ]);

        try {
            // Initialize review model
            $review = TblHomeReview::firstOrNew(['id' => $request->review_id]);
            $filePath = null;
            $bannerImagePath = null;
            //  dd($review);
            // Log request data for debugging
            \Log::info('Review Save Request:', [
                'media_type' => $request->media_type,
                'has_image' => $request->hasFile('images'),
                'has_video' => $request->hasFile('video'),
                'has_banner_image' => $request->hasFile('banner_image'),
            ]);

            // Handle media removal or type change
            if ($request->media_type === 'images') {
                // Delete existing video if switching from video
                if ($review->file && $review->file_type === 'video') {
                    Storage::disk('public')->delete('review/videos/' . $review->file);
                    $review->file = null;
                    $review->file_type = null;
                }

                // Handle image upload
                if ($request->hasFile('images')) {
                    $file = $request->file('images');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('review/images', $fileName, 'public');
                    // Delete old image if exists
                    if ($review->file && $review->file_type === 'images') {
                        Storage::disk('public')->delete('review/images/' . $review->file);
                    }
                    $review->file_type = 'images';
                    $review->file = $fileName;
                } elseif ($request->input('remove_existing_image') && $review->file && $review->file_type === 'images') {
                    // Explicitly remove existing image
                    Storage::disk('public')->delete('review/images/' . $review->file);
                    $review->file = null;
                    $review->file_type = null;
                }

                // Clear banner image if switching to images
                if ($review->banner_image) {
                    Storage::disk('public')->delete('review/banner_images/' . $review->banner_image);
                    $review->banner_image = null;
                }
            } elseif ($request->media_type === 'video') {
                // Delete existing image if switching from image
                if ($review->file && $review->file_type === 'images') {
                    Storage::disk('public')->delete('review/images/' . $review->file);
                    $review->file = null;
                    $review->file_type = null;
                }

                // Handle video upload
                if ($request->hasFile('video')) {
                    $file = $request->file('video');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('review/videos', $fileName, 'public');
                    // Delete old video if exists
                    if ($review->file && $review->file_type === 'video') {
                        Storage::disk('public')->delete('review/videos/' . $review->file);
                    }
                    $review->file_type = 'video';
                    $review->file = $fileName;
                } elseif ($request->input('remove_existing_video') && $review->file && $review->file_type === 'video') {
                    // Explicitly remove existing video
                    Storage::disk('public')->delete('review/videos/' . $review->file);
                    $review->file = null;
                    $review->file_type = null;
                } else {
                    // Log if video upload failed
                    if ($request->has('video') && !$request->hasFile('video')) {
                        \Log::error('Video upload failed: ' . $request->file('video')->getErrorMessage());
                    }
                }

                // Handle banner image upload for video
                if ($request->hasFile('banner_image')) {
                    $bannerImage = $request->file('banner_image');
                    $bannerImageName = time() . '_' . $bannerImage->getClientOriginalName();
                    $bannerImagePath = $bannerImage->storeAs('review/banner_images', $bannerImageName, 'public');
                    // Delete old banner image if exists
                    if ($review->banner_image) {
                        Storage::disk('public')->delete('review/banner_images/' . $review->banner_image);
                    }
                    $review->banner_image = $bannerImageName;
                } elseif ($request->input('remove_existing_banner_image') && $review->banner_image) {
                    // Handle banner image removal
                    Storage::disk('public')->delete('review/banner_images/' . $review->banner_image);
                    $review->banner_image = null;
                }
            } else {
                // No media type selected, remove any existing file and banner image
                if ($review->file) {
                    if ($review->file_type === 'images') {
                        Storage::disk('public')->delete('review/images/' . $review->file);
                    } elseif ($review->file_type === 'video') {
                        Storage::disk('public')->delete('review/videos/' . $review->file);
                    }
                    $review->file = null;
                    $review->file_type = null;
                }
                if ($review->banner_image) {
                    Storage::disk('public')->delete('review/banner_images/' . $review->banner_image);
                    $review->banner_image = null;
                }
            }

            // Update review details
            $review->guest_name = $request->guest_name;
            $review->review_date = $request->review_date;
            $review->rating = (float) $request->rating;
            $review->comment = $request->comment;
            $review->review_type = $request->review_type;
            $review->link = $request->link;
            $review->pType = $request->pType;
            $review->home_id = $request->property_id;
            $review->add_ip = $request->ip();
            $review->add_by = Auth::guard('admin')->user()->name;

            if ($request->pType === 'unit') {
                $review->unit_id = $request->id;
                $review->multi_unit_id = 0;
            } else {
                $review->multi_unit_id = $request->id;
                $review->unit_id = 0;
            }

            $review->save();

            return redirect()->route('pms.property.unit.or.multiunit.review', [
                'id' => $id,
                'pType' => $request->pType,
                'property_id' => $request->property_id
            ])->with('success', 'Review saved successfully!');
        } catch (\Exception $e) {
            \Log::error('Review save failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to save review: ' . $e->getMessage())->withInput();
        }
    }


    public function deleteReview($id)
    {
        try {
            $review = TblHomeReview::findOrFail($id);

            if ($review->file) {
                Storage::disk('public')->delete($review->file);
            }

            $review->delete();

            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Review deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function deleteMultipleReviews(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'status' => false,
                'message' => 'No Reviews selected for deletion.'
            ], 400);
        }
        try {
            TblHomeReview::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Review(s) deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function ShowHometoggleStatus($id)
    {
        try {
            $detail = TblHomeReview::where(['id' => $id])->first();
            TblHomeReview::where(['id' => $id])->update(['display_on_home_page' => !$detail->display_on_home_page]);
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Show home page updated successfully'
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function websiteFaq(Request $request, $id = null)
    {
        $detail = ($request->pType == 'unit') ? TblHomeUnit::find($id) : TblHomeMultiUnit::find($id);
        $parentHome = TblHome::where('id', $detail->home_id)->first(['tbl_homes.*', 'tbl_homes.home_name as unit_name']);
        if ($request->pType == 'unit') {
            $additionalCharges = WebsiteFaq::where('unit_id', $id)->get();
        } else {
            $additionalCharges = WebsiteFaq::where('multi_unit_id', $id)->get();
        }
        $charges = [];
        if ($additionalCharges->count() > 0) {
            foreach ($additionalCharges as $additionalCharge) {
                $charges[] = ['question' => $additionalCharge->question, 'answer' => $additionalCharge->answer];
            }
        } else {
            $charges = [
                ['question' => '', 'answer' => '', 'display' => false]
            ];
        }
        return view('pms.property.website-faq', compact('charges', 'id', 'parentHome', 'id', 'detail'));
    }

    public function saveWebsiteFaq(Request $request)
    {

        $request->validate([
            'property_id' => 'required|integer',
            'id' => 'required|integer',
            'pType' => 'required|in:unit,multiunit',
            'charges' => 'array',
            'charges.*.question' => 'required',
            'charges.*.answer' => 'required',

        ]);
        $unitId = $request->id;
        $pType = $request->pType;
        $charges = $request->charges;
        if ($pType === 'unit') {
            WebsiteFaq::where('unit_id', $unitId)->forceDelete();
        } else {
            WebsiteFaq::where('multi_unit_id', $unitId)->forceDelete();
        }

        foreach ($charges as $charge) {
            $detail = [
                'question' => $charge['question'],
                'answer' => $charge['answer'],
                'unit_id' => $pType === 'unit' ? $unitId : 0,
                'multi_unit_id' => $pType === 'multiunit' ? $unitId : 0,
                'pType' => $pType,
            ];
            WebsiteFaq::create($detail);
        }
        return redirect()->route('pms.property.unit.or.multiunit.websitefaq', ['id' => $request->id, 'property_id' => $request->property_id, 'pType' => $request->pType])->with('success', 'Property Faq saved successfully.');
    }

    public function updateUnitImagesInRu($id)
    {
        $query = TblHomeUnit::query();
        $query->with(['images']);
        $property = $query->find($id);
        $client = new Client();
        $headers = ['Content-Type' => 'application/xml'];
        $canSleep = $property->no_of_bedrooms * 2;
        $body = '<Push_PutProperty_RQ>
            <Authentication>
                <UserName>' . config("ru.RU_USER_NAME") . '</UserName>
                <Password>' . config("ru.RU_PASSWORD") . '</Password>
            </Authentication>
            <Property>
                <ID>' . $property->ru_property_id . '</ID>
                <OwnerID>' . config("ru.RU_OWNER_ID") . '</OwnerID>
                <IsActive>true</IsActive>
                <IsArchived>false</IsArchived>';

        $body .= '<Images>';
        foreach ($property->images as $image) {
            $body .= '<Image ImageTypeID="' . $image->tbl_ru_image_type_id . '" ImageReferenceID="' . $image->id . '">https://Unreal Estate.in/' . $image->filename . '</Image>';
        }
        $body .= '</Images>
        <ImageCaptions>';
        foreach ($property->images as $key => $image) {
            if ($image->default == 1) {
                $body .= '<ImageCaption LanguageID="1" ImageReferenceID="' . $image->id . '">' . $image->ruImageType->image_category_name . '</ImageCaption>';
            } else {
                $body .= '<ImageCaption LanguageID="1" ImageReferenceID="' . $image->id . '">' . $image->ruImageType->image_category_name . '</ImageCaption>';
            }
        }
        $body .= '</ImageCaptions>';
        $body .= '<ImageSecondaryTypes>';
        foreach ($property->images as $key => $image) {
            $body .= ' <ImageSecondaryType ImageReferenceID="' . $image->id . '" ImageSecondaryTypeID ="' . $image->tbl_ru_image_type_id . '" />';
        }
        $body .= '</ImageSecondaryTypes>';
        $body .= '</Property>
        </Push_PutProperty_RQ>';


        $request = new Request('POST', 'https://rm.rentalsunited.com/api/Handler.ashx', $headers, $body);
        $res = $client->sendAsync($request)->wait();
        $res->getBody();
        $result = simplexml_load_string($res->getBody());
        $result_json = json_encode($result);
        $result_array = json_decode($result_json, TRUE);

        if (isset($result_array['Status'])) {
            if ($result_array['Status'] == 'Success') {
                return response()->json([
                    'status' => true,
                    'data' => $result_array['ID'],
                    'message' => 'Property Published Successfully.'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'data' => '',
                    'message' => $result_array['Status']
                ], 500);
            }
        } else {
            if ($result_array['@attributes']) {
                return response()->json([
                    'status' => false,
                    'data' => '',
                    'message' => $result_array['0']
                ], 500);
            } else {
                return response()->json([
                    'status' => false,
                    'data' => '',
                    'message' => 'Something Went Wrong!'
                ], 500);
            }
        }
    }

    public function setPriceLabAuthTokenForm(){
         
         $setting = DB::table('tbl_sitesettings')->where('id', 1)->first('pricelabs_token');
         $token = null;
         if($setting){
             $token = $setting->pricelabs_token;
         }
         return view('pms.property.pricelabs-auth-token', compact('token'));
    }
    
    
    public function savePriceLabAuthToken(Request $request){
         $request->validate([
            'user_token' => 'required',
        ]);
        
        try {
          DB::table('tbl_sitesettings')->where('id', 1)->update(['pricelabs_token'=>$request->user_token]);    
          $dd = $this->priceLabService->setAuthToken($request->user_token);
         
          return redirect()->route('pms.set.pricelab.token')->with('success', 'Token saved successfully');  
        }
        catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function publishPricelab(Request $request, $id = null){
        $model = $request->pType === 'unit'? TblHomeUnit::class : TblHomeUnit::class;
        $property = $model::find($id);
        

        try {
            $listingPayload  = $this->priceLabPayloadService->preparePriceLabsListingPayload($property);

            
            $this->priceLabService->syncListings($listingPayload);

            $calendarPayload  = $this->priceLabPayloadService->preparePriceLabsCalendarPayload($property);
            $this->priceLabService->syncCalendars($calendarPayload);

            $bookings  = PropertyBooking::where('property_id', $property->id)->where('property_booking_status', '!=', 'Canceled')->get(); 
    
            foreach($bookings as $booking){
                if($booking->property_booking_status =='Confirmed'){
                    $payload = $this->priceLabPayloadService->preparePriceLabsSyncConfirmedReservation($booking);
                }
                else{
                    $payload = $this->priceLabPayloadService->preparePriceLabsSyncUnconfirmedReservation($booking);
                }
                $this->priceLabService->syncReservations($payload);
                $this->propertyService->updateAvaliabilityWithReservationHistorical($property->id, date('Y-m-d', strtotime($booking->checkin_date)), date('Y-m-d', strtotime($booking->checkout_date)), 0, 'unit',  'Booking');
            }
            
            $blocks = DB::table('ru_property_blocked')->where('ru_property_id', $property->ru_property_id)->whereNotIn('reason', ['Update booking', 'Booking'])->get();
            foreach($blocks as $block){
                $payload = $this->priceLabPayloadService->preparePriceLabsBlockUnblockPayload($property->ru_property_id, $block->date_from, $block->date_to, 0);
                $response = $this->priceLabService->syncCalendars($payload);
                
                $payload = $this->priceLabPayloadService->preparePriceLabsSyncReservationOnDateBlock($property->ru_property_id, $block->date_from, $block->date_to);
                $response = $this->priceLabService->syncReservations($payload);
            }
            $property->update([ 'price_lab_sync_date_time' => date('Y-m-d h:i:s')]);
            return response()->json([
                'success' => true,
                'message' => 'Property published successfully',
            ]);
        }
        catch (\Throwable $e) {
            dd($e->getMessage());
            \Log::error('PriceLabs sync failed (non-blocking)', [
                'property_id' => $property->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }

    public function getPrices(Request $request, $id){
        $propertyDetail = TblHomeUnit::where('ru_property_id', $id)->first();
        $response = $this->priceLabService->getPrices(['id'=>$propertyDetail->pricelabs_unique_id]);
        
        
        if(isset($response['status']) && $response['status']==false){
            return $response;
        }
        
        if(isset($response['data'])){
            foreach($response['data'] as $data){
                $detail = RuPropertyPrice::where(['ru_property_id' => $id, 'price_date' => $data['date']])->firstOrNew();
                $detail->price = $data['price'];
                $detail->type = 'unit';
                $detail->ru_property_id = $id;
                $detail->price_date = $data['date'];
                $detail->status = 1;
                $detail->save();
            }
        }
        
        $units= TblHomeUnit::whereNotNull('ru_property_id')->where('ru_property_id', $id)->get();
        foreach($units  as $unit){
            $listingId = $unit->ru_property_id;
            $priceSeasonsXml = '';
            $data = RuPropertyPrice::where('ru_property_id', $listingId)->whereDate('price_date', '>=', now()->toDateString())->get();
            foreach ($data as $row) {
                $date = $row->price_date;
                $price = $row->price;
                $priceSeasonsXml .= "
                <Season DateFrom='{$date}' DateTo='{$date}'>
                    <Price>{$price}</Price>
                    <Extra>0</Extra>
                </Season>";
            }
            if ($priceSeasonsXml) {
                $priceXml = "
                <Push_PutPrices_RQ>
                    <Authentication>
                        <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                        <Password>" . config('ru.RU_PASSWORD') . "</Password>
                    </Authentication>
                    <Prices PropertyID='{$listingId}'>
                        {$priceSeasonsXml}
                    </Prices>
                </Push_PutPrices_RQ>";
                $res = MasterHelper::makeXmlRequest($priceXml);
                
            }
        }
        
        return response()->json([
            'success' => true,
            'data'    => $response,
        ]);
    }

    public function checkPricelabsStatus(Request $request, $id){
        try {
            $propertyDetail = TblHomeUnit::where('id', $id)->first();
            
            $payload =  [
                'statuses' => [
                    [
                        'id' => (string) $propertyDetail->ru_property_id,
                        'type' => 'listing',
                    ]
                ]
            ];
            $response = $this->priceLabService->getStatuses($payload);
            
            if(isset($response['status']) && $response['status']==false){
                return $response;
            }
            
            return response()->json([
                'success' => true,
                'data'    => $response,
            ]);
        } catch (\Throwable $e) {
            \Log::error('PriceLabs Status failed', [
                'property_id' => $propertyDetail->id,
                'error'       => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch status',
            ], 500);
        }
    }
}
