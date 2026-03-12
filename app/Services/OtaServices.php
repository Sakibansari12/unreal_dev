<?php

namespace App\Services;
use App\Models\TblHome;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Models\TblLocation;
use App\Models\TblRuLocation;
use App\Models\TblHomeType;
use App\Models\RuPropertyPrice;
use App\Models\RuPropertyAvailability;
use App\Models\RuPropertyMinstay;
use App\Models\TblHomeAmenities;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\helper\MasterHelper;
use Carbon\Carbon;
use DB;
use config;
use URL;

class OtaServices{


    public function __construct(){

    }

    public function pushPropertyInOta($id, $pType){
        $url = config('app.url');
        ini_set('memory_limit', '512566778888M');
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $query = ($pType == 'unit')?TblHomeUnit::query():TblHomeMultiUnit::query();
        $query->with(['additionalCharges', 'images', 'videos', 'assets', 'features', 'reviews', 'amenities', 'ruamenities']);
        $property = $query->find($id);
        $location =  TblLocation::where('id', $property->location_id)->first();
        $ruLocation =  TblRuLocation::where('id', $location->ru_location_id)->first();
        //$ruLocation = TblRuLocation::where('id', 15)->first();
        $ruLocation =  TblRuLocation::where('ru_location_id', $location->ru_location_id)->first();
        $homeType =  TblHomeType::where('id', $property->home_type_id)->first();
        $space = $property->maximum_number_of_guests;
        $parentProperty = TblHome::where('id', $property->home_id)->first();
        if($pType == 'unit'){
          $cancellationSlab = DB::table('cancellation_slabs')->where('unit_id', $property->id)->get();
        }
        else{
          $cancellationSlab = DB::table('cancellation_slabs')->where('multi_unit_id', $property->id)->get();
        }

        $homeType = DB::table('tbl_home_types')->where('id', $property->home_type_id)->first();
        $parentHome = TblHome::where('id', $property->home_id)->first();

        if(!$ruLocation){
            return response()->json([
                'status' => false,
                'data' =>'',
                'message' => 'Incorrect Location Mapping'
            ], 500);
        }

        if(!$property->images || $property->images->count() < 10){
            return response()->json([
                'status' => false,
                'data' =>'',
                'message' => 'Add atleast 10 images'
            ], 500);
        }

        if($property->ruamenities->count() ==0){
            return response()->json([
                'status' => false,
                'data' =>'',
                'message' => 'Please Add Ammenity'
            ], 500);
        }

        if($pType == 'unit'){
           $roomAmmenities = DB::table('tbl_ru_amenity_mappings')->where('unit_id', $property->id)->whereNull('deleted_at')->get();
        }
        else{
            $roomAmmenities = DB::table('tbl_ru_amenity_mappings')->where('multi_unit_id', $property->id)->whereNull('deleted_at')->get();
        }


        if($roomAmmenities->count()==0){
            return response()->json([
                'status' => false,
                'data' =>'',
                'message' => 'Please Add Room Ammenities in floor section'
            ], 500);
        }

        if($pType == 'unit'){
            $homeAmmeniies = DB::table('tbl_home_amenities')->where('unit_id', $property->id)->whereNull('deleted_at')->get();
        }
        else{
            $homeAmmeniies = DB::table('tbl_home_amenities')->where('multi_unit_id', $property->id)->whereNull('deleted_at')->get();
        }

        $serverFloorId =  $roomAmmenities->first()->floor_id;
        $ruFloor = DB::table('tbl_ru_floor')->where('floor_id', $serverFloorId)->first()->floor_id;

        $client = new Client();
        $headers = ['Content-Type' => 'application/xml'];
        if($property->no_of_bedrooms){
            $canSleep =$property->no_of_bedrooms*2;
        }
        else{
            $canSleep =2;
        }
        $body = '<Push_PutProperty_RQ>
                <Authentication>
                    <UserName>'.config("ru.RU_USER_NAME").'</UserName>
                    <Password>'.config("ru.RU_PASSWORD").'</Password>
                </Authentication>
                <Property>';
        if($property->ru_building_id ){
            $body .='<PUID BuildingID="'.$property->ru_building_id.'">1</PUID>';
        }
        else{
            $body .='<PUID BuildingID="-1">1</PUID>';
        }
        
        
        $ptypeid = $property->no_of_room==1 ?1 :53;

        $body .='<Name>'.$property->unit_name.'</Name>
                <OwnerID>'.config("ru.RU_OWNER_ID").'</OwnerID>
                <DetailedLocationID TypeID="'.$ruLocation->ru_location_type_id.'">'.$ruLocation->ru_location_id.'</DetailedLocationID>
                <IsActive>true</IsActive>
                <IsArchived>false</IsArchived>

                <StandardGuests>'.$canSleep.'</StandardGuests>
                <CanSleepMax>'.$canSleep.'</CanSleepMax>
                <PropertyTypeID>'.$ptypeid.'</PropertyTypeID>
                <ObjectTypeID>'.$homeType->ru_property_type_id.'</ObjectTypeID>';


        if($property->ru_building_id ){
            $body .='<NoOfUnits>'.$property->no_of_rooms.'</NoOfUnits>';
        }
        else{
            $body .='<NoOfUnits>1</NoOfUnits>';
        }

        $body .='<Floor>'.$ruFloor.'</Floor>';
            $amnityXml = '';
            foreach($homeAmmeniies as $ammenity){
                $ruAmmenity = DB::table('tbl_ru_amenities')->where('amenities_id', $ammenity->amenities_id)->first();
                $amnityXml .= '<Amenity Count="1">'.$ruAmmenity->ru_amenities_id.'</Amenity>';
            }
            $body .='<Amenities>'.$amnityXml.'</Amenities>';
            $body .='<Street>'.$property->location.'</Street>
                <ZipCode>'.$property->postal_code.'</ZipCode>
                <Coordinates>
                    <Longitude>'.$parentHome->map_latitude.'</Longitude>
                    <Latitude>'.$parentHome->map_longitude.'</Latitude>
                </Coordinates>
            <Images>';
            foreach($property->images as $image){
                $body .='<Image ImageTypeID="'.$image->tbl_ru_image_type_id.'" ImageReferenceID="'.$image->id.'">'.$url.'/'.$image->filename.'</Image>';
            }
            $body .='</Images>
            <ImageCaptions>';
            foreach($property->images as $key => $image){
                if($image->default==1){
                    $body .='<ImageCaption LanguageID="1" ImageReferenceID="'.$image->id.'">'.$image->ruImageType->image_category_name.'</ImageCaption>';
                }
                else{
                    $ruImages = DB::table('tbl_ru_image_types')->where('ru_image_type_id', $image->ru_image_type_id)->first();
                    if($ruImages){
                        $body .='<ImageCaption LanguageID="1" ImageReferenceID="'.$image->id.'">'.$image->ruImageType->image_category_name.'</ImageCaption>';
                    }
                    else{
                        $body .='<ImageCaption LanguageID="1" ImageReferenceID="'.$image->id.'">Interiors</ImageCaption>';
                    }
                }
            }
            $body .='</ImageCaptions>';
            $body .='<ImageSecondaryTypes>';
            foreach($property->images as $key => $image){
               $body .=' <ImageSecondaryType ImageReferenceID="'.$image->id.'" ImageSecondaryTypeID ="'.$image->tbl_ru_image_type_id.'" />';
            }

            $body .='</ImageSecondaryTypes>
                <ArrivalInstructions>
                    <Landlord>'.env("APP_NAME").'</Landlord>
                    <Email>'.env("APP_NAME").'</Email>
                    <Phone>'.env("APP_NAME").'</Phone>
                    <DaysBeforeArrival>2</DaysBeforeArrival>
                    <HowToArrive>
                        <Text LanguageID="1">Contact with service provider</Text>
                    </HowToArrive>
                    <PickupService>
                        <Text LanguageID="1">Contact with service provider</Text>
                    </PickupService>
                </ArrivalInstructions>
                <CheckInOut>
                    <CheckInFrom>'.$property->checkin_time.'</CheckInFrom>
                    <CheckInTo>'.$property->checkout_time.'</CheckInTo>
                    <CheckOutUntil>'.$property->checkout_time.'</CheckOutUntil>
                    <Place>'.$parentHome->home_type.'</Place>
                </CheckInOut>
                <PaymentMethods>
                    <PaymentMethod PaymentMethodID="1">Account number: '.config("ru.RU_ACCOUNT_NO").'</PaymentMethod>
                </PaymentMethods>
                <TermsAndConditionsLinks>
                    <TermsAndConditionsLink LanguageID="1">'.$url.'</TermsAndConditionsLink>
                </TermsAndConditionsLinks>
                <Deposit DepositTypeID="3">'.$property->per_night_price.'</Deposit>';
                $body .='<CancellationPolicies>';
                if($cancellationSlab->count()> 0){
                    foreach($cancellationSlab as $cslab){
                        $body .=' <CancellationPolicy ValidFrom="'.$cslab->slab_from.'" ValidTo="'.$cslab->slab_to.'">'.(integer)$cslab->slab.'</CancellationPolicy>';
                    }
                }
                else{
                    $body .=' <CancellationPolicy ValidFrom="1" ValidTo="1">100</CancellationPolicy>';
                }
                $body .='</CancellationPolicies>';
                $body .='<Descriptions>
                     <Description LanguageID="1">
                       <Text>'.$property->ru_description.'</Text>
                        <HouseRules>'.$property->house_rules.'</HouseRules>
                     </Description>
                   </Descriptions>
                <CompositionRoomsAmenities>';

                for($bathroomNo =1; $bathroomNo <= (integer)$property->no_of_bathrooms; $bathroomNo++){
                    $body .='<CompositionRoomAmenities CompositionRoomID="81"><Amenities>';
                    $bathRoomAmmenites = $roomAmmenities->where('amenity_type', 'Bathroom')->where('bathroom_no', $bathroomNo);
                    foreach($bathRoomAmmenites as $bathRoomAmmenity){
                        $ruAmmenities = DB::table('tbl_ru_amenities')->where('amenities_id', $bathRoomAmmenity->ru_amenity_id)->whereNull('deleted_at')->first();
                        $body .='<Amenity Count="1">'.$ruAmmenities->ru_amenities_id.'</Amenity>';
                    }
                    $body .='</Amenities></CompositionRoomAmenities>';
                }

                for($bedroomNo =1; $bedroomNo <= (integer)$property->no_of_bedrooms; $bedroomNo++){
                    $body .='<CompositionRoomAmenities CompositionRoomID="257"><Amenities> <Amenity Count="1">61</Amenity>';
                    $bedRoomAmmenites = $roomAmmenities->where('amenity_type', 'Bedroom')->where('bedroom_no', $bedroomNo);
                    foreach($bedRoomAmmenites as $bedRoomAmmenity){
                        $ruAmmenities = DB::table('tbl_ru_amenities')->where('amenities_id', $bedRoomAmmenity->ru_amenity_id)->whereNull('deleted_at')->first();
                        $body .='<Amenity Count="1">'.$ruAmmenities->ru_amenities_id.'</Amenity>';
                    }
                    $body .='</Amenities></CompositionRoomAmenities>';
                }

                if($pType == 'unit'){
                    $otherRuRooms = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('unit_id', $id)->groupBy('ru_room_id')->get();
                }
                else{
                    $otherRuRooms = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('multi_unit_id', $id)->groupBy('ru_room_id')->get();
                }


                foreach($otherRuRooms as $otheroom){
                    $body .='<CompositionRoomAmenities CompositionRoomID="'.$otheroom->ru_room_id.'"><Amenities> <Amenity Count="1">'.$otheroom->ru_room_id.'</Amenity>';
                    $otherRuRoomsAmmenity = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('unit_id', $id)->where('ru_room_id', $otheroom->ru_room_id)->get();
                    foreach($otherRuRoomsAmmenity as $ammenity){
                        $body .='<Amenity Count="1">'.$ammenity->ammenity_id.'</Amenity>';
                    }
                    $body .='</Amenities></CompositionRoomAmenities>';
                }
                $body .='</CompositionRoomsAmenities><LicenceInfo>
                    <LicenceNumber>'.config("ru.RU_LICENCE_NO").'</LicenceNumber>
                </LicenceInfo>
                </Property>
            </Push_PutProperty_RQ>';


        $request = new Request('POST', config('ru.RU_URL'), $headers, $body);
        $res = $client->sendAsync($request)->wait();
        $res->getBody();
        $result = simplexml_load_string($res->getBody());
        $result_json = json_encode($result);
        $result_array = json_decode($result_json,TRUE);


        if(isset($result_array['Status']) ){
            if($result_array['Status']=='Success'){
                SELF::updateRoomAmmenities($property->id, $pType);
                SELF::syncAmmenities($property->id, $pType);
                $property->ru_property_id = $result_array['ID'];
                $property->is_published = 1;
                $property->save();
                SELF::pushAvaliabilityPriceMinstay($result_array['ID']);
                return response()->json([
                    'status' => true,
                    'data' =>$result_array['ID'],
                    'message' => 'Property Published Successfully.'
                ], 200);
            }
            else{
                return response()->json([
                    'status' => false,
                    'data' =>'',
                    'message' => $result_array['Status']
                ], 500);
            }
        }
        else{
            if($result_array['@attributes']){
                return response()->json([
                    'status' => false,
                    'data' =>'',
                    'message' => $result_array['0']
                ], 500);
            }
            else{
                return response()->json([
                    'status' => false,
                    'data' =>'',
                    'message' => 'Something Went Wrong!'
                ], 500);

            }
        }
    }



    public function pushPropertyInOtaImages($id, $pType){
        $url = config('app.url');
        ini_set('memory_limit', '512566778888M');
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $query = ($pType == 'unit')?TblHomeUnit::query():TblHomeMultiUnit::query();
        $query->with(['additionalCharges', 'images', 'videos', 'assets', 'features', 'reviews', 'amenities', 'ruamenities']);
        $property = $query->find($id);
        $location =  TblLocation::where('id', $property->location_id)->first();
        $ruLocation =  TblRuLocation::where('id', $location->ru_location_id)->first();
        $homeType =  TblHomeType::where('id', $property->home_type_id)->first();
        $space = $property->maximum_number_of_guests;
        $parentProperty = TblHome::where('id', $property->home_id)->first();
        if($pType == 'unit'){
          $cancellationSlab = DB::table('cancellation_slabs')->where('unit_id', $property->id)->get();
        }
        else{
          $cancellationSlab = DB::table('cancellation_slabs')->where('multi_unit_id', $property->id)->get();
        }

        $homeType = DB::table('tbl_home_types')->where('id', $property->home_type_id)->first();
        $parentHome = TblHome::where('id', $property->home_id)->first();

        if(!$ruLocation){
            return response()->json([
                'status' => false,
                'data' =>'',
                'message' => 'Incorrect Location Mapping'
            ], 500);
        }

        if(!$property->images || $property->images->count() < 10){
            return response()->json([
                'status' => false,
                'data' =>'',
                'message' => 'Add atleast 10 images'
            ], 500);
        }

        if($property->ruamenities->count() ==0){
            return response()->json([
                'status' => false,
                'data' =>'',
                'message' => 'Please Add Ammenity'
            ], 500);
        }

        if($pType == 'unit'){
           $roomAmmenities = DB::table('tbl_ru_amenity_mappings')->where('unit_id', $property->id)->whereNull('deleted_at')->get();
        }
        else{
            $roomAmmenities = DB::table('tbl_ru_amenity_mappings')->where('multi_unit_id', $property->id)->whereNull('deleted_at')->get();
        }


        if($roomAmmenities->count()==0){
            return response()->json([
                'status' => false,
                'data' =>'',
                'message' => 'Please Add Room Ammenities in floor section'
            ], 500);
        }

        if($pType == 'unit'){
            $homeAmmeniies = DB::table('tbl_home_amenities')->where('unit_id', $property->id)->whereNull('deleted_at')->get();
        }
        else{
            $homeAmmeniies = DB::table('tbl_home_amenities')->where('multi_unit_id', $property->id)->whereNull('deleted_at')->get();
        }

        $serverFloorId =  $roomAmmenities->first()->floor_id;
        $ruFloor = DB::table('tbl_ru_floor')->where('floor_id', $serverFloorId)->first()->floor_id;

        $client = new Client();
        $headers = ['Content-Type' => 'application/xml'];
        if($property->no_of_bedrooms){
            $canSleep =$property->no_of_bedrooms*2;
        }
        else{
            $canSleep =2;
        }
        $body = '<Push_PutProperty_RQ>
                <Authentication>
                    <UserName>'.config("ru.RU_USER_NAME").'</UserName>
                    <Password>'.config("ru.RU_PASSWORD").'</Password>
                </Authentication>
                <Property>';
        if($property->ru_building_id ){
            $body .='<PUID BuildingID="'.$property->ru_building_id.'">1</PUID>';
        }
        else{
            $body .='<PUID BuildingID="-1">1</PUID>';
        }

        $body .='<Name>'.$property->unit_name.'</Name>
                <OwnerID>'.config("ru.RU_OWNER_ID").'</OwnerID>
                <DetailedLocationID TypeID="'.$ruLocation->ru_location_type_id.'">'.$ruLocation->ru_location_id.'</DetailedLocationID>
                <IsActive>true</IsActive>
                <IsArchived>false</IsArchived>

                <StandardGuests>'.$canSleep.'</StandardGuests>
                <CanSleepMax>'.$canSleep.'</CanSleepMax>
                <PropertyTypeID>'.$homeType->ru_property_type_id.'</PropertyTypeID>';


        if($property->ru_building_id ){
            $body .='<NoOfUnits>'.$property->no_of_rooms.'</NoOfUnits>';
        }
        else{
            $body .='<NoOfUnits>1</NoOfUnits>';
        }

        $body .='<Floor>'.$ruFloor.'</Floor>';
            $amnityXml = '';
            foreach($homeAmmeniies as $ammenity){
                $ruAmmenity = DB::table('tbl_ru_amenities')->where('amenities_id', $ammenity->amenities_id)->first();
                $amnityXml .= '<Amenity Count="1">'.$ruAmmenity->ru_amenities_id.'</Amenity>';
            }
            $body .='<Amenities>'.$amnityXml.'</Amenities>';
            $body .='<Street>'.$property->location.'</Street>
                <ZipCode>'.$property->postal_code.'</ZipCode>
                <Coordinates>
                    <Longitude>28.7041</Longitude>
                    <Latitude>77.1025</Latitude>
                </Coordinates>
            <Images>';
            foreach($property->images as $image){
                $body .='<Image ImageTypeID="'.$image->tbl_ru_image_type_id.'" ImageReferenceID="'.$image->id.'">'.$url.'/'.$image->filename.'</Image>';
            }
            $body .='</Images>
            <ImageCaptions>';
            foreach($property->images as $key => $image){
                if($image->default==1){
                    $body .='<ImageCaption LanguageID="1" ImageReferenceID="'.$image->id.'">'.$image->ruImageType->image_category_name.'</ImageCaption>';
                }
                else{
                    $ruImages = DB::table('tbl_ru_image_types')->where('ru_image_type_id', $image->ru_image_type_id)->first();
                    if($ruImages){
                        $body .='<ImageCaption LanguageID="1" ImageReferenceID="'.$image->id.'">'.$image->ruImageType->image_category_name.'</ImageCaption>';
                    }
                    else{
                        $body .='<ImageCaption LanguageID="1" ImageReferenceID="'.$image->id.'">Interiors</ImageCaption>';
                    }
                }
            }
            $body .='</ImageCaptions>';
            $body .='<ImageSecondaryTypes>';
            foreach($property->images as $key => $image){
               $body .=' <ImageSecondaryType ImageReferenceID="'.$image->id.'" ImageSecondaryTypeID ="'.$image->tbl_ru_image_type_id.'" />';
            }

            $body .='</ImageSecondaryTypes>
                <ArrivalInstructions>
                    <Landlord>'.env("APP_NAME").'</Landlord>
                    <Email>'.env("APP_NAME").'</Email>
                    <Phone>'.env("APP_NAME").'</Phone>
                    <DaysBeforeArrival>2</DaysBeforeArrival>
                    <HowToArrive>
                        <Text LanguageID="1">Information about arrival</Text>
                    </HowToArrive>
                    <PickupService>
                        <Text LanguageID="1">Information about pickup</Text>
                    </PickupService>
                </ArrivalInstructions>
                <CheckInOut>
                    <CheckInFrom>'.$property->checkin_time.'</CheckInFrom>
                    <CheckInTo>'.$property->checkout_time.'</CheckInTo>
                    <CheckOutUntil>'.$property->checkout_time.'</CheckOutUntil>
                    <Place>Apartmant</Place>
                </CheckInOut>
                <PaymentMethods>
                    <PaymentMethod PaymentMethodID="1">Account number: '.config("ru.RU_ACCOUNT_NO").'</PaymentMethod>
                </PaymentMethods>
                <TermsAndConditionsLinks>
                    <TermsAndConditionsLink LanguageID="1">'.$url.'</TermsAndConditionsLink>
                </TermsAndConditionsLinks>
                <Deposit DepositTypeID="3">'.$property->per_night_price.'</Deposit>';
                $body .='<CancellationPolicies>';
                if($cancellationSlab->count()> 0){
                    foreach($cancellationSlab as $cslab){
                        $body .=' <CancellationPolicy ValidFrom="'.$cslab->slab_from.'" ValidTo="'.$cslab->slab_to.'">'.(integer)$cslab->slab.'</CancellationPolicy>';
                    }
                }
                else{
                    $body .=' <CancellationPolicy ValidFrom="1" ValidTo="1">100</CancellationPolicy>';
                }
                $body .='</CancellationPolicies>';
                $body .='<Descriptions>
                     <Description LanguageID="1">
                       <Text>'.$property->ru_description.'</Text>
                        <HouseRules>'.$property->house_rules.'</HouseRules>
                     </Description>
                   </Descriptions>
                <CompositionRoomsAmenities>';

                for($bathroomNo =1; $bathroomNo <= (integer)$property->no_of_bathrooms; $bathroomNo++){
                    $body .='<CompositionRoomAmenities CompositionRoomID="81"><Amenities>';
                    $bathRoomAmmenites = $roomAmmenities->where('amenity_type', 'Bathroom')->where('bathroom_no', $bathroomNo);
                    foreach($bathRoomAmmenites as $bathRoomAmmenity){
                        $ruAmmenities = DB::table('tbl_ru_amenities')->where('amenities_id', $bathRoomAmmenity->ru_amenity_id)->whereNull('deleted_at')->first();
                        $body .='<Amenity Count="1">'.$ruAmmenities->ru_amenities_id.'</Amenity>';
                    }
                    $body .='</Amenities></CompositionRoomAmenities>';
                }

                for($bedroomNo =1; $bedroomNo <= (integer)$property->no_of_bedrooms; $bedroomNo++){
                    $body .='<CompositionRoomAmenities CompositionRoomID="257"><Amenities> <Amenity Count="1">61</Amenity>';
                    $bedRoomAmmenites = $roomAmmenities->where('amenity_type', 'Bedroom')->where('bedroom_no', $bedroomNo);
                    foreach($bedRoomAmmenites as $bedRoomAmmenity){
                        $ruAmmenities = DB::table('tbl_ru_amenities')->where('amenities_id', $bedRoomAmmenity->ru_amenity_id)->whereNull('deleted_at')->first();
                        $body .='<Amenity Count="1">'.$ruAmmenities->ru_amenities_id.'</Amenity>';
                    }
                    $body .='</Amenities></CompositionRoomAmenities>';
                }

                if($pType == 'unit'){
                    $otherRuRooms = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('unit_id', $id)->groupBy('ru_room_id')->get();
                }
                else{
                    $otherRuRooms = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('multi_unit_id', $id)->groupBy('ru_room_id')->get();
                }


                foreach($otherRuRooms as $otheroom){
                    $body .='<CompositionRoomAmenities CompositionRoomID="'.$otheroom->ru_room_id.'"><Amenities> <Amenity Count="1">'.$otheroom->ru_room_id.'</Amenity>';
                    $otherRuRoomsAmmenity = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('unit_id', $id)->where('ru_room_id', $otheroom->ru_room_id)->get();
                    foreach($otherRuRoomsAmmenity as $ammenity){
                        $body .='<Amenity Count="1">'.$ammenity->ammenity_id.'</Amenity>';
                    }
                    $body .='</Amenities></CompositionRoomAmenities>';
                }
                $body .='</CompositionRoomsAmenities><LicenceInfo>
                    <LicenceNumber>'.config("ru.RU_LICENCE_NO").'</LicenceNumber>
                </LicenceInfo>
                </Property>
            </Push_PutProperty_RQ>';


        $request = new Request('POST', config('ru.RU_URL'), $headers, $body);
        $res = $client->sendAsync($request)->wait();
        $res->getBody();
        $result = simplexml_load_string($res->getBody());
        $result_json = json_encode($result);
        $result_array = json_decode($result_json,TRUE);


        if(isset($result_array['Status']) ){
            if($result_array['Status']=='Success'){
                SELF::updateRoomAmmenities($property->id, $pType);
                SELF::syncAmmenities($property->id, $pType);
                $property->ru_property_id = $result_array['ID'];
                $property->is_published = 1;
                $property->save();
                SELF::pushAvaliabilityPriceMinstay($result_array['ID']);
                return response()->json([
                    'status' => true,
                    'data' =>$result_array['ID'],
                    'message' => 'Property Published Successfully.'
                ], 200);
            }
            else{
                return response()->json([
                    'status' => false,
                    'data' =>'',
                    'message' => $result_array['Status']
                ], 500);
            }
        }
        else{
            if($result_array['@attributes']){
                return response()->json([
                    'status' => false,
                    'data' =>'',
                    'message' => $result_array['0']
                ], 500);
            }
            else{
                return response()->json([
                    'status' => false,
                    'data' =>'',
                    'message' => 'Something Went Wrong!'
                ], 500);

            }
        }
    }


    public function updateRoomAmmenities($id, $pType){
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $query = ($pType == 'unit')?TblHomeUnit::query():TblHomeMultiUnit::query();
        $property = $query->find($id);

        $location =  TblLocation::where('id', $property->location_id)->first();
        $ruLocation =  TblRuLocation::where('id', $location->ru_location_id)->first();
        $homeType =  TblHomeType::where('id', $property->home_type_id)->first();
        $space = $property->maximum_number_of_guests;
        $parentProperty = TblHome::where('id', $property->home_id)->first();


        if($pType == 'unit'){
            $cancellationSlab = DB::table('cancellation_slabs')->where('unit_id', $property->id)->get();
        }
        else{
            $cancellationSlab = DB::table('cancellation_slabs')->where('multi_unit_id', $property->id)->get();
        }

        $homeType = DB::table('tbl_home_types')->where('id', $property->home_type_id)->first();
        if($pType == 'unit'){
            $roomAmmenities = DB::table('tbl_ru_amenity_mappings')->where('unit_id', $property->id)->whereNull('deleted_at')->get();
        }
        else{
           $roomAmmenities = DB::table('tbl_ru_amenity_mappings')->where('multi_unit_id', $property->id)->whereNull('deleted_at')->get();
        }

        $serverFloorId =  $roomAmmenities->first()->floor_id;
        $ruFloor = DB::table('tbl_ru_floor')->where('floor_id', $serverFloorId)->first();

        $client = new Client();
        $headers = ['Content-Type' => 'application/xml'];
        $canSleep =$property->no_of_bedrooms*2;
        $body = '<Push_PutProperty_RQ>
            <Authentication>
                <UserName>'.config("ru.RU_USER_NAME").'</UserName>
                <Password>'.config("ru.RU_PASSWORD").'</Password>
            </Authentication>
            <Property>
                <ID>'.$property->ru_property_id.'</ID>
                <OwnerID>'.config("ru.RU_OWNER_ID").'</OwnerID>
                <IsActive>true</IsActive>
                <IsArchived>false</IsArchived>';

        $body .='<Floor>'.$ruFloor->ru_id.'</Floor>';

        $body .='<CompositionRoomsAmenities>';
        for($bathroomNo =1; $bathroomNo <= (integer)$property->no_of_bathrooms; $bathroomNo++){
            $body .='<CompositionRoomAmenities CompositionRoomID="81"><Amenities>';
            $bathRoomAmmenites = $roomAmmenities->where('amenity_type', 'Bathroom')->where('bathroom_no', $bathroomNo);
            foreach($bathRoomAmmenites as $bathRoomAmmenity){
                $ruAmmenities = DB::table('tbl_ru_amenities')->where('amenities_id', $bathRoomAmmenity->ru_amenity_id)->whereNull('deleted_at')->first();
                $body .='<Amenity Count="1">'.$ruAmmenities->ru_amenities_id.'</Amenity>';
            }
            $body .='</Amenities></CompositionRoomAmenities>';
        }

        for($bedroomNo =1; $bedroomNo <= (integer)$property->no_of_bedrooms; $bedroomNo++){
            $body .='<CompositionRoomAmenities CompositionRoomID="257"><Amenities> <Amenity Count="1">61</Amenity>';
            $bedRoomAmmenites = $roomAmmenities->where('amenity_type', 'Bedroom')->where('bedroom_no', $bedroomNo);
            foreach($bedRoomAmmenites as $bedRoomAmmenity){
                $ruAmmenities = DB::table('tbl_ru_amenities')->where('amenities_id', $bedRoomAmmenity->ru_amenity_id)->whereNull('deleted_at')->first();
                $body .='<Amenity Count="1">'.$ruAmmenities->ru_amenities_id.'</Amenity>';
            }
            $body .='</Amenities></CompositionRoomAmenities>';
        }


        if($pType == 'unit'){
            $otherRuRooms = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('unit_id', $id)->groupBy('ru_room_id')->get();
        }
        else{
            $otherRuRooms = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('multi_unit_id', $id)->groupBy('ru_room_id')->get();
        }

        foreach($otherRuRooms as $otheroom){
            $body .='<CompositionRoomAmenities CompositionRoomID="'.$otheroom->ru_room_id.'"><Amenities> <Amenity Count="1">'.$otheroom->ru_room_id.'</Amenity>';
            $otherRuRoomsAmmenity = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('unit_id', $id)->where('ru_room_id', $otheroom->ru_room_id)->get();
            foreach($otherRuRoomsAmmenity as $ammenity){
                $body .='<Amenity Count="1">'.$ammenity->ammenity_id.'</Amenity>';
            }
            $body .='</Amenities></CompositionRoomAmenities>';
        }

        $body  .='</CompositionRoomsAmenities></Property></Push_PutProperty_RQ>';

        $request = new Request('POST', config('ru.RU_URL'), $headers, $body);
        $res = $client->sendAsync($request)->wait();
        $res->getBody();
        $result = simplexml_load_string($res->getBody());
        $result_json = json_encode($result);
        $result_array = json_decode($result_json,TRUE);
        if(isset($result_array['Status']) ){
            if($result_array['Status']=='Success'){
                return response()->json([
                    'status' => true,
                    'data' =>$result_array['ID'],
                    'message' => 'Property Publish Successfully.'
                ], 200);
            }
            else{
                return response()->json([
                    'status' => false,
                    'data' =>'',
                    'message' => $result_array['Status']
                ], 500);
            }
        }
        else{
            if($result_array['@attributes']){
                return response()->json([
                    'status' => false,
                    'data' =>'',
                    'message' => $result_array['0']
                ], 500);
            }
            else{
                return response()->json([
                    'status' => false,
                    'data' =>'',
                    'message' => 'Something Went Wrong!'
                ], 500);

            }
        }
    }


    public function syncAmmenities($id, $pType){

        $query = ($pType =='unit')?TblHomeUnit::query():TblHomeMultiUnit::query();
        $query->with(['additionalCharges', 'images', 'videos', 'assets', 'features', 'reviews', 'amenities', 'ruamenities']);
        $property = $query->find($id);


        $location =  TblLocation::where('id', $property->location_id)->first();
        $ruLocation =  TblRuLocation::where('id', $location->ru_location_id)->first();
        $homeType =  TblHomeType::where('id', $property->home_type_id)->first();
        $space = $property->maximum_number_of_guests;
        $parentProperty = TblHome::where('id', $property->home_id)->first();


        if($pType =='unit'){
           $homeAmmeniies = TblHomeAmenities::where('unit_id', $property->id)->get();
        }
        else{
            $homeAmmeniies = TblHomeAmenities::where('multi_unit_id', $property->id)->get();
        }
        $client = new Client();
        $headers = ['Content-Type' => 'application/xml'];
        $canSleep =$property->no_of_bedrooms*2;
        $body = '<Push_PutProperty_RQ>
            <Authentication>
                <UserName>'.config("ru.RU_USER_NAME").'</UserName>
                <Password>'.config("ru.RU_PASSWORD").'</Password>
            </Authentication>
            <Property>
                <ID>'.$property->ru_property_id.'</ID>
                <OwnerID>'.config("ru.RU_OWNER_ID").'</OwnerID>
                <IsActive>true</IsActive>
                <IsArchived>false</IsArchived>';
                $amnityXml = '';
                foreach($homeAmmeniies as $ammenity){
                    $ruAmmenity = DB::table('tbl_ru_amenities')->where('amenities_id', $ammenity->amenities_id)->first();
                    $amnityXml .= '<Amenity Count="1">'.$ruAmmenity->ru_amenities_id.'</Amenity>';
                }

        $body .='<Amenities>'.$amnityXml.'</Amenities>';
        $body .='</Property></Push_PutProperty_RQ>';



        $request = new Request('POST', config('ru.RU_URL'), $headers, $body);
        $res = $client->sendAsync($request)->wait();
        $res->getBody();
        $result = simplexml_load_string($res->getBody());
        $result_json = json_encode($result);
        $result_array = json_decode($result_json,TRUE);
        
      

        if(isset($result_array['Status']) ){
            if($result_array['Status']=='Success'){
                return response()->json([
                    'status' => true,
                    'data' =>$result_array['ID'],
                    'message' => 'Property Published Successfully.'
                ], 200);
            }
            else{
                return response()->json([
                    'status' => false,
                    'data' =>'',
                    'message' => $result_array['Status']
                ], 500);
            }
        }
        else{
            if($result_array['@attributes']){
                return response()->json([
                    'status' => false,
                    'data' =>'',
                    'message' => $result_array['0']
                ], 500);
            }
            else{
                return response()->json([
                    'status' => false,
                    'data' =>'',
                    'message' => 'Something Went Wrong!'
                ], 500);

            }
        }
    }


    public function pushAvaliabilityPriceMinstay($ruproperid){
        set_time_limit(0);
        $i = 160;
        $date_from = date('Y-m-d');
        $date_to = date('Y-m-d', strtotime($date_from . ' +'.$i.' day'));
        $units = TblHomeUnit::where('ru_property_id', $ruproperid)->get();

        if($units){
            foreach($units as $unit){
                RuPropertyPrice::where('property_id', $unit->id)->where('type', 'unit')->delete();
                RuPropertyMinstay::where('home_id', $unit->id)->where('type', 'unit')->delete();
                RuPropertyAvailability::where('property_id', $unit->id)->where('type', 'unit')->delete();
                $avaliabilityArray = array('ru_property_id'=>$unit->ru_property_id, 'is_available'=>'yes', 'property_id'=>$unit->id);
                for($i=0; $i<=180; $i++){
                    $avaliabilityDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                    $avaliabilityArray['availability_date'] = $avaliabilityDate;
                    $avaliabilityArray['type'] = 'unit';
                    RuPropertyAvailability::create($avaliabilityArray);
                }
                $xmlAvaliability = "<Push_PutAvbUnits_RQ>
                            <Authentication>
                                <UserName>".config('ru.RU_USER_NAME')."</UserName>
                                <Password>".config('ru.RU_PASSWORD')."</Password>
                            </Authentication>
                            <MuCalendar PropertyID='".$unit->ru_property_id."'>
                                <Date From='".$date_from."' To='".$date_to."'>
                                    <U>1</U>
                                    <C>4</C>
                                </Date>
                            </MuCalendar>
                        </Push_PutAvbUnits_RQ>";
                $xmlResponse = MasterHelper::makeXmlRequest($xmlAvaliability);

                if(isset($unit->per_night_price)){
                    $priceArray = array('price'=>$unit->per_night_price, 'property_id'=>$unit->id, 'ru_property_id'=>$unit->ru_property_id);
                    for($i=0; $i<=180; $i++){
                        $price_date = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                        $priceArray['price_date'] = $price_date;
                        $priceArray['type'] = 'unit';
                        RuPropertyPrice::create($priceArray);
                    }
                }

                $priceXml = "<Push_PutPrices_RQ>
                        <Authentication>
                        <UserName>".config('ru.RU_USER_NAME')."</UserName>
                        <Password>".config('ru.RU_PASSWORD')."</Password>
                        </Authentication>
                        <Prices PropertyID='".$unit->ru_property_id."'>
                        <Season DateFrom='".$date_from."' DateTo='".$date_to."'>
                            <Price>".$unit->per_night_price."</Price>
                            <Extra>0</Extra>
                        </Season>
                        </Prices>
                </Push_PutPrices_RQ>";
                $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($priceXml);


                $minStayArray = array('ru_property_id'=>$unit->ru_property_id, 'is_minstay_count'=>$unit->min_stay, 'home_id'=>$unit->id);
                for($i=0; $i<=180; $i++){
                    $date = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                    $minStayArray['minstay_date'] = $date;
                    $minStayArray['is_minstay_count'] = $unit->min_stay;
                    $minStayArray['type'] = 'unit';
                    RuPropertyMinstay::create($minStayArray);
                }


                $minStayXml = "<Push_PutAvbUnits_RQ>
                    <Authentication>
                    <UserName>".config('ru.RU_USER_NAME')."</UserName>
                    <Password>".config('ru.RU_PASSWORD')."</Password>
                    </Authentication>
                    <MuCalendar PropertyID='".$unit->ru_property_id."'>
                    <Date From='".$date_from."' To='".$date_to."'>
                        <U>1</U>
                        <MS>".$unit->min_stay."</MS>
                        <C>4</C>
                    </Date>
                    </MuCalendar>
                </Push_PutAvbUnits_RQ>";
                $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($minStayXml);

            }
        }

        $munits = TblHomeMultiUnit::where('ru_property_id', $ruproperid)->get();
        if($munits){
            foreach($munits as $unit){
                RuPropertyPrice::where('property_id', $unit->id)->where('type', 'multiunit')->delete();
                RuPropertyMinstay::where('home_id', $unit->id)->where('type', 'multiunit')->delete();
                RuPropertyAvailability::where('property_id', $unit->id)->where('type', 'multiunit')->delete();
                $avaliabilityArray = array('ru_property_id'=>$unit->ru_property_id, 'is_available'=>'yes', 'property_id'=>$unit->id);
                for($i=0; $i<=180; $i++){
                    $avaliabilityDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                    $avaliabilityArray['availability_date'] = $avaliabilityDate;
                    $avaliabilityArray['type'] = 'multiunit';
                    RuPropertyAvailability::create($avaliabilityArray);
                }

                $xmlAvaliability = "<Push_PutAvbUnits_RQ>
                            <Authentication>
                                <UserName>".config('ru.RU_USER_NAME')."</UserName>
                                <Password>".config('ru.RU_PASSWORD')."</Password>
                            </Authentication>
                            <MuCalendar PropertyID='".$unit->ru_property_id."'>
                                <Date From='".$date_from."' To='".$date_to."'>
                                    <U>1</U>
                                    <C>4</C>
                                </Date>
                            </MuCalendar>
                        </Push_PutAvbUnits_RQ>";
                $xmlResponse = MasterHelper::makeXmlRequest($xmlAvaliability);

                if(isset($unit->per_night_price)){
                    $priceArray = array('price'=>$unit->per_night_price, 'property_id'=>$unit->id, 'ru_property_id'=>$unit->ru_property_id);
                    for($i=0; $i<=180; $i++){
                        $price_date = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                        $priceArray['price_date'] = $price_date;
                        $priceArray['type'] = 'multiunit';
                        RuPropertyPrice::create($priceArray);
                    }
                }

                $priceXml = "<Push_PutPrices_RQ>
                        <Authentication>
                        <UserName>".config('ru.RU_USER_NAME')."</UserName>
                        <Password>".config('ru.RU_PASSWORD')."</Password>
                        </Authentication>
                        <Prices PropertyID='".$unit->ru_property_id."'>
                        <Season DateFrom='".$date_from."' DateTo='".$date_to."'>
                            <Price>".$unit->per_night_price."</Price>
                            <Extra>0</Extra>
                        </Season>
                        </Prices>
                </Push_PutPrices_RQ>";

                $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($priceXml);


                $minStayArray = array('ru_property_id'=>$unit->ru_property_id, 'is_minstay_count'=>$unit->min_stay, 'home_id'=>$unit->id);
                for($i=0; $i<=180; $i++){
                    $date = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                    $minStayArray['minstay_date'] = $date;
                    $minStayArray['is_minstay_count'] = $unit->min_stay;
                    $minStayArray['type'] = 'multiunit';
                    RuPropertyMinstay::create($minStayArray);
                }


                $minStayXml = "<Push_PutAvbUnits_RQ>
                    <Authentication>
                    <UserName>".config('ru.RU_USER_NAME')."</UserName>
                    <Password>".config('ru.RU_PASSWORD')."</Password>
                    </Authentication>
                    <MuCalendar PropertyID='".$unit->ru_property_id."'>
                    <Date From='".$date_from."' To='".$date_to."'>
                        <U>1</U>
                        <MS>".$unit->min_stay."</MS>
                        <C>4</C>
                    </Date>
                    </MuCalendar>
                </Push_PutAvbUnits_RQ>";
                $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($minStayXml);
           }
        }
    }



    public function websitepushProperty($id, $pType){

        ini_set('memory_limit', '512566778888M');
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $query = ($pType == 'unit')?TblHomeUnit::query():TblHomeMultiUnit::query();
        $query->with(['additionalCharges', 'images', 'videos', 'assets', 'features', 'reviews', 'amenities', 'ruamenities']);
        $property = $query->find($id);



        $location =  TblLocation::where('id', $property->location_id)->first();
        $ruLocation =  TblRuLocation::where('id', $location->ru_location_id)->first();
        $homeType =  TblHomeType::where('id', $property->home_type_id)->first();
        $space = $property->maximum_number_of_guests;
        $parentProperty = TblHome::where('id', $property->home_id)->first();
        if($pType == 'unit'){
          $cancellationSlab = DB::table('cancellation_slabs')->where('unit_id', $property->id)->get();
        }
        else{
          $cancellationSlab = DB::table('cancellation_slabs')->where('multi_unit_id', $property->id)->get();
        }

        $homeType = DB::table('tbl_home_types')->where('id', $property->home_type_id)->first();
        $parentHome = TblHome::where('id', $property->home_id)->first();

        if(!$ruLocation){
            return response()->json([
                'status' => false,
                'data' =>'',
                'message' => 'Incorrect Location Mapping'
            ], 500);
        }

         if(!$property->images || $property->images->count() < 10){
            return response()->json([
                'status' => false,
                'data' =>'',
                'message' => 'Add atleast 10 images'
            ], 500);
        }

        if($property->ruamenities->count() ==0){
            return response()->json([
                'status' => false,
                'data' =>'',
                'message' => 'Please Add Ammenity'
            ], 500);
        }

        if($pType == 'unit'){
           $roomAmmenities = DB::table('tbl_ru_amenity_mappings')->where('unit_id', $property->id)->whereNull('deleted_at')->get();
        }
        else{
            $roomAmmenities = DB::table('tbl_ru_amenity_mappings')->where('multi_unit_id', $property->id)->whereNull('deleted_at')->get();
        }


        if($roomAmmenities->count()==0){
            return response()->json([
                'status' => false,
                'data' =>'',
                'message' => 'Please Add Room Ammenities in floor section'
            ], 500);
        }

        if($property){
            //$property->ru_property_id = $result_array['ID'];
            //  $property->website_ru_property_id = ;
            $nextId = ($pType == 'unit')
                ? TblHomeUnit::max('ru_property_id')
                : TblHomeMultiUnit::max('ru_property_id');

            $nextId = $nextId ? $nextId + 1 : 1;
           // dd($nextId);
            $property->website_is_published = 1;
            $property->ru_property_id = $nextId;
            $property->save();
            SELF::pushWebsiteAvaliabilityPriceMinstay($nextId);
            return response()->json([
                'status' => true,
                'data' =>$nextId,
                'message' => 'Property Website Published Successfully.'
            ], 200);
        }
        else{
            return response()->json([
                'status' => false,
                'data' =>'',
                'message' => 'Something Went Wrong!'
            ], 500);
        }

    }


    public function pushWebsiteAvaliabilityPriceMinstay($ruproperid){
        set_time_limit(0);
        $i = 160;
        $date_from = date('Y-m-d');
        $date_to = date('Y-m-d', strtotime($date_from . ' +'.$i.' day'));
        $units = TblHomeUnit::where('ru_property_id', $ruproperid)->get();

        if($units){
            foreach($units as $unit){
                RuPropertyPrice::where('property_id', $unit->id)->where('type', 'unit')->delete();
                RuPropertyMinstay::where('home_id', $unit->id)->where('type', 'unit')->delete();
                RuPropertyAvailability::where('property_id', $unit->id)->where('type', 'unit')->delete();
                $avaliabilityArray = array('ru_property_id'=>$unit->ru_property_id, 'is_available'=>'yes', 'property_id'=>$unit->id);
                for($i=0; $i<=180; $i++){
                    $avaliabilityDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                    $avaliabilityArray['availability_date'] = $avaliabilityDate;
                    $avaliabilityArray['type'] = 'unit';
                    RuPropertyAvailability::create($avaliabilityArray);
                }
                if(isset($unit->per_night_price)){
                    $priceArray = array('price'=>$unit->per_night_price, 'property_id'=>$unit->id, 'ru_property_id'=>$unit->ru_property_id);
                    for($i=0; $i<=180; $i++){
                        $price_date = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                        $priceArray['price_date'] = $price_date;
                        $priceArray['type'] = 'unit';
                        RuPropertyPrice::create($priceArray);
                    }
                }
                $minStayArray = array('ru_property_id'=>$unit->ru_property_id, 'is_minstay_count'=>$unit->min_stay, 'home_id'=>$unit->id);
                for($i=0; $i<=180; $i++){
                    $date = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                    $minStayArray['minstay_date'] = $date;
                    $minStayArray['is_minstay_count'] = $unit->min_stay;
                    $minStayArray['type'] = 'unit';
                    RuPropertyMinstay::create($minStayArray);
                }

            }
        }

        $munits = TblHomeMultiUnit::where('ru_property_id', $ruproperid)->get();
        if($munits){
            foreach($munits as $unit){
                RuPropertyPrice::where('property_id', $unit->id)->where('type', 'multiunit')->delete();
                RuPropertyMinstay::where('home_id', $unit->id)->where('type', 'multiunit')->delete();
                RuPropertyAvailability::where('property_id', $unit->id)->where('type', 'multiunit')->delete();
                $avaliabilityArray = array('ru_property_id'=>$unit->ru_property_id, 'is_available'=>'yes', 'property_id'=>$unit->id);
                for($i=0; $i<=180; $i++){
                    $avaliabilityDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                    $avaliabilityArray['availability_date'] = $avaliabilityDate;
                    $avaliabilityArray['type'] = 'multiunit';
                    RuPropertyAvailability::create($avaliabilityArray);
                }
                if(isset($unit->per_night_price)){
                    $priceArray = array('price'=>$unit->per_night_price, 'property_id'=>$unit->id, 'ru_property_id'=>$unit->ru_property_id);
                    for($i=0; $i<=180; $i++){
                        $price_date = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                        $priceArray['price_date'] = $price_date;
                        $priceArray['type'] = 'multiunit';
                        RuPropertyPrice::create($priceArray);
                    }
                }
                $minStayArray = array('ru_property_id'=>$unit->ru_property_id, 'is_minstay_count'=>$unit->min_stay, 'home_id'=>$unit->id);
                for($i=0; $i<=180; $i++){
                    $date = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                    $minStayArray['minstay_date'] = $date;
                    $minStayArray['is_minstay_count'] = $unit->min_stay;
                    $minStayArray['type'] = 'multiunit';
                    RuPropertyMinstay::create($minStayArray);
                }
           }
        }
    }


    public function updateUnitImagesInRu($id){

        $query = TblHomeUnit::query();
        $url = config('app.url');
        $query->with(['images']);
        $property = $query->find($id);
        $client = new Client();
        $headers = ['Content-Type' => 'application/xml'];



        $body = '<Push_PutProperty_RQ>
            <Authentication>
                <UserName>'.config("ru.RU_USER_NAME").'</UserName>
                <Password>'.config("ru.RU_PASSWORD").'</Password>
            </Authentication>
            <Property>
                <ID>'.$property->ru_property_id.'</ID>
                <OwnerID>'.config("ru.RU_OWNER_ID").'</OwnerID>
                <IsActive>true</IsActive>
                <IsArchived>false</IsArchived>';

        $body .='<Images>';
        foreach($property->images as $image){
            $body .='<Image ImageTypeID="'.$image->tbl_ru_image_type_id.'" ImageReferenceID="'.$image->id.'">'.$url.'/'.$image->filename.'</Image>';
        }
        $body .='</Images>
        <ImageCaptions>';
        foreach($property->images as $key => $image){
            if($image->default==1){
                $body .='<ImageCaption LanguageID="1" ImageReferenceID="'.$image->id.'">'.$image->ruImageType->image_category_name.'</ImageCaption>';
            }
            else{
                $body .='<ImageCaption LanguageID="1" ImageReferenceID="'.$image->id.'">'.$image->ruImageType->image_category_name.'</ImageCaption>';
            }
        }
        $body .='</ImageCaptions>';
        $body .='<ImageSecondaryTypes>';
        foreach($property->images as $key => $image){
           $body .=' <ImageSecondaryType ImageReferenceID="'.$image->id.'" ImageSecondaryTypeID ="'.$image->tbl_ru_image_type_id.'" />';
        }
        $body .='</ImageSecondaryTypes>';
        $body .='</Property>
        </Push_PutProperty_RQ>';


        $request = new Request('POST', 'https://rm.rentalsunited.com/api/Handler.ashx', $headers, $body);
        $res = $client->sendAsync($request)->wait();
        $res->getBody();
        $result = simplexml_load_string($res->getBody());
        $result_json = json_encode($result);
        $result_array = json_decode($result_json,TRUE);

        if(isset($result_array['Status']) ){
            if($result_array['Status']=='Success'){
                return response()->json([
                    'status' => true,
                    'data' =>$result_array['ID'],
                    'message' => 'Property Published Successfully.'
                ], 200);
            }
            else{
                return response()->json([
                    'status' => false,
                    'data' =>'',
                    'message' => $result_array['Status']
                ], 500);
            }
        }
        else{
            if($result_array['@attributes']){
                return response()->json([
                    'status' => false,
                    'data' =>'',
                    'message' => $result_array['0']
                ], 500);
            }
            else{
                return response()->json([
                    'status' => false,
                    'data' =>'',
                    'message' => 'Something Went Wrong!'
                ], 500);

            }
        }
    }



}
