<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Models\TblHomeAmenities;
use App\Models\TblRuAmenity;
use App\Models\TblRuAmenityMapping;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;




class PropertyFetureData
{
   
    public $numBedrooms, $numBathrooms = 2;
    public $unit_id;
    public $bathroomAmenities = [];
    public $pType = 'unit';
    public $bathammenities = [];
    public $bedammenities = [];
    public $bedroomAmenities = [];

    public function mount($unit_id, $pType){
        
    $this->unit_id = $unit_id;
    $this->pType = $pType;

    if ($this->pType == 'unit') {
        $home = TblHomeUnit::find($this->unit_id);
    } else {
        $home = TblHomeMultiUnit::find($this->unit_id);
    }

    if (!$home) {
        return null;
    }

    $this->numBedrooms = $home->no_of_bedrooms;
    $this->numBathrooms = $home->no_of_bathrooms;

    $floorQuery = TblRuAmenityMapping::query();
    if ($this->pType == 'unit') {
        $floor = $floorQuery->where('unit_id', $this->unit_id)->groupBy('floor_id')->first('floor_id');
    } else {
        $floor = $floorQuery->where('multi_unit_id', $this->unit_id)->groupBy('floor_id')->first('floor_id');
    }

    $floorId = $floor ? $floor->floor_id : null;

    // Bedroom amenities list
    $bedRoomAmenityList = DB::table('tbl_ru_amenities')->where('amenities_type', 'Bedroom')->get();
    $this->bedammenities = [];
    foreach ($bedRoomAmenityList as $bedroomValue) {
        $this->bedammenities[] = [
            'amenities_id' => $bedroomValue->amenities_id, 
            'amenities_name' => $bedroomValue->amenities_name
        ];
    }

    
    $bathRoomAmenityList = DB::table('tbl_ru_amenities')->where('amenities_type', 'Bathroom')->get();
    $this->bathammenities = [];
    foreach ($bathRoomAmenityList as $bathroomValue) {
        $this->bathammenities[] = [
            'amenities_id' => $bathroomValue->amenities_id, 
            'amenities_name' => $bathroomValue->amenities_name
        ];
    }

   
    $bedroomAmenities = [];
    foreach (range(1, $this->numBedrooms) as $index) {
        $bedroomAmenitiesQuery = TblRuAmenityMapping::query();
        if ($this->pType == 'unit') {
            $bedroomAmenitiesQuery->where('unit_id', $this->unit_id);
        } else {
            $bedroomAmenitiesQuery->where('multi_unit_id', $this->unit_id);
        }

        $amenityIds = $bedroomAmenitiesQuery->where('amenity_type', 'Bedroom')->where('bedroom_no', $index)->pluck('ru_amenity_id')->toArray();
        $bedroomAmenities[$index - 1] = DB::table('tbl_ru_amenities')->whereIn('amenities_id', $amenityIds)->pluck('amenities_name')->toArray();
    }

   
    $bathroomAmenities = [];
    foreach (range(1, $this->numBathrooms) as $index) {
        $bathroomAmenitiesQuery = TblRuAmenityMapping::query();
        if ($this->pType == 'unit') {
            $bathroomAmenitiesQuery->where('unit_id', $this->unit_id);
        } else {
            $bathroomAmenitiesQuery->where('multi_unit_id', $this->unit_id);
        }

        $amenityIds = $bathroomAmenitiesQuery->where('amenity_type', 'Bathroom')->where('bathroom_no', $index)->pluck('ru_amenity_id')->toArray();
        $bathroomAmenities[$index - 1] = DB::table('tbl_ru_amenities')->whereIn('amenities_id', $amenityIds)->pluck('amenities_name')->toArray();
    }

    return [
        'bedroomAmenities' => $bedroomAmenities,
        'bathroomAmenities' => $bathroomAmenities
    ];
}


    
}

