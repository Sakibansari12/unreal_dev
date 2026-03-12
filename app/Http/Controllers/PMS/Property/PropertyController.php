<?php

namespace App\Http\Controllers\PMS\Property;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\TblHome;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Models\PropertyBooking;
use App\Models\TblState;
use App\Models\TblHomeType;
use App\Models\TblLocation;
use App\Models\TblArea;
use App\Services\PropertyService;
use App\helper\MasterHelper;
use Carbon\Carbon;
use Session;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Str;


class PropertyController extends Controller
{

    protected $propertyService;

    public function __construct()
    {
        $this->propertyService = new PropertyService();
    }


    // public function list(Request $request){
    //     ini_set('memory_limit', '50000M');
    //   // $query = TblHome::query();
    //     $query = $this->propertyService->applyUserRoleFilter(TblHome::query());

    //     $dropdownQuery = clone $query;

    //     $query->when(!empty($request->search), function ($q) use ($request) {
    //         $q->where('home_name', 'like', '%' . $request->search . '%');
    //     });

    //     // $search = $request->get('search', '');
    //     // if (!empty($search)) {
    //     //     $query->where(function ($query) use ($search) {
    //     //         $query->where('home_name', 'like', $search . '%')
    //     //             ->orWhere('location', 'like', $search . '%')
    //     //             ->orWhere('state', 'like', $search . '%');
    //     //     });
    //     // }


    //   //  $propertList = $query->with(['units', 'multiUnits', 'images'])->orderBy('id', 'desc')->get();


    //   // $propertList = $query->with(['units', 'multiUnits', 'images', 'user.parent'])->orderBy('id', 'desc')->get();
    //     $propertList = $query->with(['units', 'multiUnits', 'user.parent'])->orderBy('id', 'desc')->paginate(30)->withQueryString();

    //     $propertList->map(function ($home) {
    //         $user = $home->user;
    //         if ($user) {
    //             if ($user->role_id == 7) {
    //                 $home->property_manager_name = $user->name;
    //             } elseif ($user->parent && $user->parent->role_id == 7) {
    //                 $home->property_manager_name = $user->parent->role;
    //             } else {
    //                 $home->property_manager_name = '';
    //             }
    //         } else {
    //             $home->property_manager_name = '';
    //         }
    //         return $home;
    //     });

    //     // if (!empty($request->property_manager)) {
    //     //     $propertList = $propertList->filter(function ($home) use ($request) {
    //     //         return str_contains(strtolower($home->property_manager_name), strtolower($request->property_manager));
    //     //     })->values();
    //     // }


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





    //     //dropdown 

    //   $propertyDropdownList = $dropdownQuery->with(['user.parent'])->orderBy('id', 'desc')->get();
    //   $propertyDropdownList->map(function ($home) {
    //         $user = $home->user;
    //         if ($user) {
    //             if ($user->role_id == 7) {
    //                 $home->property_manager_name = $user->name;
    //             } elseif ($user->parent && $user->parent->role_id == 7) {
    //                 $home->property_manager_name = $user->parent->role;
    //             } else {
    //                 $home->property_manager_name = '';
    //             }
    //         } else {
    //             $home->property_manager_name = '';
    //         }
    //         return $home;
    //     });


    //  $user =  Auth::guard('admin')->user();
    //     return view('pms.property.list',compact('propertList', 'propertyDropdownList','user'));
    // }

    public function list(Request $request)
    {
        ini_set('memory_limit', '50000M');

        // 🔹 Base query with role filter
        $query = $this->propertyService
            ->applyUserRoleFilter(TblHome::query());

        // 🔹 Clone for dropdown
        $dropdownQuery = clone $query;

        // 🔍 Property name filter
        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where('home_name', 'like', '%' . $request->search . '%');
        });

        // 🔍 Property manager filter (DB level)
        $query->when($request->filled('property_manager'), function ($q) use ($request) {
            $q->whereHas('user', function ($uq) use ($request) {
                $uq->where('role_id', 7)
                    ->where('name', 'like', '%' . $request->property_manager . '%')
                    ->orWhereHas('parent', function ($pq) use ($request) {
                        $pq->where('role_id', 7)
                            ->where('name', 'like', '%' . $request->property_manager . '%');
                    });
            });
        });

        // 🔹 Main list (pagination SAFE)
        $propertList = $query
            ->with(['units', 'multiUnits', 'user.parent'])
            ->orderBy('id', 'desc')
            ->paginate(30)
            ->withQueryString();

        // 🔹 Attach property manager name (display only)
        $propertList->getCollection()->transform(function ($home) {
            $user = $home->user;

            if ($user && $user->role_id == 7) {
                $home->property_manager_name = $user->name;
            } elseif ($user && $user->parent && $user->parent->role_id == 7) {
                $home->property_manager_name = $user->parent->name;
            } else {
                $home->property_manager_name = '';
            }

            return $home;
        });

        // 🔽 Dropdown list
        $propertyDropdownList = $dropdownQuery
            ->with('user.parent')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($home) {
                $user = $home->user;

                if ($user && $user->role_id == 7) {
                    $home->property_manager_name = $user->name;
                } elseif ($user && $user->parent && $user->parent->role_id == 7) {
                    $home->property_manager_name = $user->parent->name;
                } else {
                    $home->property_manager_name = null;
                }

                return $home;
            });

        $user = Auth::guard('admin')->user();

        return view(
            'pms.property.list',
            compact('propertList', 'propertyDropdownList', 'user')
        );
    }

    public function managerPropertyList(Request $request, $name)
    {
        ini_set('memory_limit', '50000M');

        $query = $this->propertyService->applyUserRoleFilter(TblHome::query());

        $query->when(!empty($request->search), function ($q) use ($request) {
            $q->where('home_name', 'like', '%' . $request->search . '%');
        });


        $propertList = $query->with(['units', 'multiUnits', 'images', 'user.parent'])->orderBy('id', 'desc')->get();

        $propertList = $propertList->map(function ($home) {
            $user = $home->user;
            if ($user) {
                if ($user->role_id == 7) {
                    $home->property_manager_name = $user->name;
                } elseif ($user->parent && $user->parent->role_id == 7) {
                    $home->property_manager_name = $user->parent->name;
                } else {
                    $home->property_manager_name = '';
                }
            } else {
                $home->property_manager_name = '';
            }
            return $home;
        });

        $propertList = $propertList->filter(function ($home) use ($name) {
            return strtolower($home->property_manager_name) === strtolower($name);
        })->values();

        return view('pms.property.manager_list', compact('propertList', 'name'));
    }



    public function form($id = null)
    {
        $detail = TblHome::with(['user.parent'])->find($id);
        if ($detail) {
            $user = $detail->user;
            if ($user) {
                if ($user->role_id == 7) {
                    $detail->property_manager_name = $user->name;
                } elseif ($user->parent && $user->parent->role_id == 7) {
                    $detail->property_manager_name = $user->parent->name;
                } else {
                    $detail->property_manager_name = '';
                }
            } else {
                $detail->property_manager_name = '';
            }
        }



        $states = TblState::where('status', 1)->get();
        $homeTypes = TblHomeType::where('status', 1)->get();
        $locations = [];
        if ($detail && $detail->state_id) {
            $locations = TblLocation::where('state_id', $detail->state_id)->where('status', 1)->get();
        }
        $areas = [];
        if ($detail && $detail->location_id) {
            $areas = TblArea::where('location_id', $detail->location_id)
                ->where('status', 1)
                ->get();
        }


        return view('pms.property.save-property-form', compact('detail', 'states', 'homeTypes', 'locations', 'areas'));
    }

    public function getAreas($location_id)
    {
        $areas = TblArea::where('location_id', $location_id)
            ->where('status', 1)
            ->get();

        return response()->json($areas);
    }



    public function save(Request $request)
    {

        $request->validate([
            'home_name' => 'required',
            'home_type_id' => 'required',
            'state_id' => 'required',
            'location_id' => 'required',
            'area_id' => 'required',
            'map_blurb' => 'required',
            'postal_code' => 'required',
            'address' => 'required',
            'description' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        try {
            $user = Auth::guard('admin')->user();
            if ($request->id) {
                $home = TblHome::findOrFail($request->id);
                $message = 'Property updated successfully.';
            } else {
                $home = new TblHome();
                $message = 'Property saved successfully.';
            }
            $state = TblState::find($request->state_id);
            $location = TblLocation::find($request->location_id);
            $area = TblArea::find($request->area_id);
            $home->home_name = $request->home_name;
            $home->state_id = $request->state_id;
            $home->state = $state ? $state->name : '';
            $home->location_id = $request->location_id;
            $home->area_id = $request->area_id;
            $home->map_text = $request->map_blurb;
            $home->area = $area ? $area->area_name : '';
            $home->location = $location ? $location->location_name : '';
            $home->description = $request->description;
            $home->allow_building = $request->home_type_id;
            $home->type = 'with_unit';
            $home->address = $request->address;
            $home->postal_code = $request->postal_code;
            $home->map_latitude = $request->latitude;
            $home->map_longitude = $request->longitude;
            $home->ru_description = $request->ru_description;

            if ($request->id == '') {
                $home->user_id = $user->id;
                $home->parent_user_id = ($user->role_id == 7) ? $user->id : $user->parent_user_id;
            }

            $home->add_ip = $request->ip();
            $home->url_key = \Str::slug($request->home_name);
            if ($request->home_type_id) {
                $home->home_type_id = $request->home_type_id;
                $home->home_type = TblHomeType::find($request->home_type_id)?->name;
            }
            $home->save();

        if ($request->id) {
            TblHomeUnit::where('home_id', $home->id)->update([
                'home_type_id' => $home->home_type_id,
                'home_type'    => $home->home_type,
                'location_id'    => $home->location_id,
                'location'    => $home->location,
                'state_id'    => $home->state_id,
                'state'    => $home->state,
                'area_id' => $home->area_id,
                'area' => $home->area,
            ]);
            TblHomeMultiUnit::where('home_id', $home->id)->update([
                'home_type_id' => $home->home_type_id,
                'home_type'    => $home->home_type,
                'location_id'    => $home->location_id,
                'location'    => $home->location,
                'state_id'    => $home->state_id,
                'state'    => $home->state,
                'area_id' => $home->area_id,
                'area' => $home->area,
            ]);
        }




            return redirect()->route('pms.property.list')->with('success', $message);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }


    public function toggleStatus($id)
    {
        try {
            $detail = TblHome::where(['id' => $id])->first();
            TblHome::where(['id' => $id])->update(['status' => !$detail->status]);
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

    public function delete($id)
    {
        try {
            $home =  TblHome::find($id);
            $isDelete = true;
            $units = TblHomeUnit::where('home_id', $home->id)->get();
            foreach ($units as $unit) {
                $hasFutureConfirmedBooking = PropertyBooking::where('property_id', $unit->id)
                    ->where('checkin_date', '>=', Carbon::today())
                    ->where('property_booking_status', 'Confirmed')->where('pType', 'unit')
                    ->exists();
                if ($hasFutureConfirmedBooking) {
                    $isDelete = false;
                    break;
                }
            }
            $multiunits = TblHomeMultiUnit::where('home_id', $home->id)->get();
            foreach ($multiunits as $munit) {
                $hasFutureConfirmedBooking = PropertyBooking::where('property_id', $munit->id)
                    ->where('checkin_date', '>=', Carbon::today())
                    ->where('property_booking_status', 'Confirmed')->where('pType', 'multiunit')
                    ->exists();
                if ($hasFutureConfirmedBooking) {
                    $isDelete = false;
                    break;
                }
            }

            if ($isDelete) {
                TblHomeUnit::where('home_id', $home->id)->delete();
                TblHomeMultiUnit::where('home_id', $home->id)->delete();
                $home->delete();
                return response()->json([
                    'status' => true,
                    'data' => '',
                    'message' => 'Property deleted successfully'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'data' => '',
                    'message' => "Property can't be delete due to confirm booking in unit or multiunit"
                ], 200);
            }
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
            TblHome::whereIn('id', $ids)->delete();
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

    public function getLocationByState($state_id)
    {
        return TblLocation::where('state_id', $state_id)->where('status', 1)->get();
    }

    public function channelManagerValidate(Request $request)
    {
        $request->validate([
            'channel_manager' => 'required',
            'property' => 'required',
        ]);

        $xml = "<CM_LNM_OrderMinimumContentQualityCheck_RQ>
            <Authentication>
                <UserName>" . config("ru.RU_USER_NAME") . "</UserName>
                <Password>" . config("ru.RU_PASSWORD") . "</Password>
            </Authentication>
            <ChannelID>" . $request->channel_manager . "</ChannelID>
            <PropertyID>" . $request->property . "</PropertyID>
        </CM_LNM_OrderMinimumContentQualityCheck_RQ>";


        $response = MasterHelper::makeXmlRequest($xml);

        $message = "Unexpected error, contact IT or try again";
        if (isset($response['data']['Status'])) {
            $message = $response['data']['Status'];
        }
        return redirect()->back()->withInput()->with('status', $message);
    }
}
