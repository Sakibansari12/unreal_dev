<?php

namespace App\Http\Controllers\PMS\Property;

use App\Http\Controllers\Controller;
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
use App\Http\Controllers\PMS\Property\SyncPropertyInRuPriceController;
use App\Http\Controllers\ScriptController;
use Carbon\Carbon;
use DB; 
use App\helper\MasterHelper;

class SyncPropertyInRuController extends Controller{

    public function index(Request $request){

    }


    public function syncPropertyInRuUnit($id){
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $query = TblHomeUnit::query();
        $query->with(['additionalCharges', 'images', 'videos', 'assets', 'features', 'reviews', 'amenities', 'ruamenities']);
        $property = $query->find($id);
        $location =  TblLocation::where('id', $property->location_id)->first();
        $ruLocation =  TblRuLocation::where('id', $location->ru_location_id)->first();
        $homeType =  TblHomeType::where('id', $property->home_type_id)->first();
        $space = $property->maximum_number_of_guests;
        $parentProperty = TblHome::where('id', $property->home_id)->first();


        $cancellationSlab = DB::table('cancellation_slabs')->where('unit_id', $property->id)->get();
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

        $roomAmmenities = DB::table('tbl_ru_amenity_mappings')->where('unit_id', $property->id)->whereNull('deleted_at')->get();
        if($roomAmmenities->count()==0){
            return response()->json([
                'status' => false,
                'data' =>'',
                'message' => 'Please Add Room Ammenities in floor section'
            ], 500);
        }


        $homeAmmeniies = DB::table('tbl_home_amenities')->where('unit_id', $property->id)->whereNull('deleted_at')->get();

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
            $body .='<Street>'.$property->address.'</Street>
                <ZipCode>'.$property->postal_code.'</ZipCode>
                <Coordinates>
                    <Longitude>28.7041</Longitude>
                    <Latitude>77.1025</Latitude>
                </Coordinates>
            <Images>';
            foreach($property->images as $image){
                $body .='<Image ImageTypeID="'.$image->tbl_ru_image_type_id.'" ImageReferenceID="'.$image->id.'">'.env("APP_URL").'/'.$image->filename.'</Image>';
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

            $body .='</ImageSecondaryTypes>
                <ArrivalInstructions>
                    <Landlord>Increud</Landlord>
                    <Email>incured@gmail.com</Email>
                    <Phone>9999999999</Phone>
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
                    <PaymentMethod PaymentMethodID="1">Account number: 000000000000000</PaymentMethod>
                    <PaymentMethod PaymentMethodID="2" >VISA, MASTERCARD, AMERICAN EXPRESS</PaymentMethod>
                </PaymentMethods>
                <TermsAndConditionsLinks>
                    <TermsAndConditionsLink LanguageID="1">'.env("APP_URL").'</TermsAndConditionsLink>
                </TermsAndConditionsLinks>
                <Deposit DepositTypeID="3">'.$property->per_night_price.'</Deposit>';
                $body .='<CancellationPolicies>';
                if($cancellationSlab->count()> 0){
                    foreach($cancellationSlab as $cslab){
                        $body .=' <CancellationPolicy ValidFrom="'.$cslab->slab_from.'" ValidTo="'.$cslab->slab_to.'">'.(integer)$cslab->slab.'</CancellationPolicy>';
                    }
                }
                else{
                    $body .=' <CancellationPolicy ValidFrom="1" ValidTo="7">40</CancellationPolicy>';
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
                $otherRuRooms = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('unit_id', $id)->groupBy('ru_room_id')->get();
        
                foreach($otherRuRooms as $otheroom){
                    $body .='<CompositionRoomAmenities CompositionRoomID="'.$otheroom->ru_room_id.'"><Amenities> <Amenity Count="1">'.$otheroom->ru_room_id.'</Amenity>';
                    $otherRuRoomsAmmenity = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('unit_id', $id)->where('ru_room_id', $otheroom->ru_room_id)->get();
                    foreach($otherRuRoomsAmmenity as $ammenity){
                        $body .='<Amenity Count="1">'.$ammenity->ammenity_id.'</Amenity>';
                    }
                    $body .='</Amenities></CompositionRoomAmenities>';
                }
                $body .='</CompositionRoomsAmenities><LicenceInfo>
                    <LicenceNumber>1222233444</LicenceNumber>
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
                SELF::updateRoomAmmenitiesUnit($property->id);
                SELF::syncUnitAmmenities($property->id);
                $property->ru_property_id = $result_array['ID'];
                $property->is_published = 1;
                $property->save();
                // $dateFrom  = date('Y-m-d');
                // $dateTo = date('Y-m-d', strtotime($dateFrom . '+180 day'));
                // $postAvaliability = "<Push_PutAvbUnits_RQ>
                //     <Authentication>
                //         <UserName>".config('ru.RU_USER_NAME')."</UserName>
                //         <Password>".config('ru.RU_PASSWORD')."</Password>
                //     </Authentication>
                //     <MuCalendar PropertyID='".$result_array['ID']."'>
                //         <Date From='".$dateFrom."' To='".$dateTo."'>
                //             <U>1</U>
                //             <C>4</C>
                //         </Date>
                //     </MuCalendar>
                // </Push_PutAvbUnits_RQ>";
                // $request = new Request('POST', config.ru(''), $headers, $postAvaliability);
                // $res = $client->sendAsync($request)->wait();
                // $postprice = "<Push_PutPrices_RQ>
                //         <Authentication>
                //         <UserName>".config('ru.RU_USER_NAME')."</UserName>
                //         <Password>".config('ru.RU_PASSWORD')."</Password>
                //         </Authentication>
                //         <Prices PropertyID='".$result_array['ID']."'>
                //         <Season DateFrom='".$dateFrom."' DateTo='".$dateTo."'>
                //             <Price>".$property->per_night_price."</Price>
                //             <Extra>0</Extra>
                //         </Season>
                //         </Prices>
                //     </Push_PutPrices_RQ>";
                // $request = new Request('POST', config.ru(''), $headers, $postprice);
                // $res = $client->sendAsync($request)->wait();
                
                
                $controller = new ScriptController();
                $controller->changeAvaliability($result_array['ID']);
                
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

    public function syncPropertyInRuMultiUnit($id){
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $query = TblHomeMultiUnit::query();
        $query->with([ 'images', 'ruamenities']);
        $property = $query->find($id);
        $location =  TblLocation::where('id', $property->location_id)->first();
        $ruLocation =  TblRuLocation::where('id', $location->ru_location_id)->first();
        $homeType =  TblHomeType::where('id', $property->home_type_id)->first();
        $space = $property->maximum_number_of_guests;
        $parentProperty = TblHome::where('id', $property->home_id)->first();

        $cancellationSlab = DB::table('cancellation_slabs')->where('multi_unit_id', $property->id)->get();
        $homeType = DB::table('tbl_home_types')->where('id', $property->home_type_id)->first();

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

        $roomAmmenities = DB::table('tbl_ru_amenity_mappings')->where('multi_unit_id', $property->id)->whereNull('deleted_at')->get();
        if($roomAmmenities->count()==0){
            return response()->json([
                'status' => false,
                'data' =>'',
                'message' => 'Please Add Room Ammenities in floor section'
            ], 500);
        }


        $homeAmmeniies = DB::table('tbl_home_amenities')->where('multi_unit_id', $property->id)->whereNull('deleted_at')->get();

        $serverFloorId =  $roomAmmenities->first()->floor_id;
        $ruFloor = DB::table('tbl_ru_floor')->where('floor_id', $serverFloorId)->first()->floor_id;

        $client = new Client();
        $headers = ['Content-Type' => 'application/xml'];
        $canSleep =$property->maximum_number_of_guests;
        $body = '<Push_PutProperty_RQ>
                <Authentication>
                    <UserName>'.config("ru.RU_USER_NAME").'</UserName>
                    <Password>'.config("ru.RU_PASSWORD").'</Password>
                </Authentication>
                <Property>
                <PUID BuildingID="-1">1</PUID>
                <Name>'.$property->unit_name.'</Name>
                <OwnerID>'.config("ru.RU_OWNER_ID").'</OwnerID>
                <DetailedLocationID TypeID="'.$ruLocation->ru_location_type_id.'">5677</DetailedLocationID>
                <IsActive>true</IsActive>
                <IsArchived>false</IsArchived>
              
                <StandardGuests>'.$canSleep.'</StandardGuests>
                <CanSleepMax>'.$canSleep.'</CanSleepMax>
                <PropertyTypeID>'.$homeType->ru_property_type_id.'</PropertyTypeID>
                <NoOfUnits>1</NoOfUnits>
                <Floor>'.$ruFloor.'</Floor>';
 
                $amnityXml = '';
                foreach($homeAmmeniies as $ammenity){
                    $ruAmmenity = DB::table('tbl_ru_amenities')->where('amenities_id', $ammenity->amenities_id)->first();
                    $amnityXml .= '<Amenity Count="1">'.$ruAmmenity->ru_amenities_id.'</Amenity>';
                }
                $body .='<Amenities>'.$amnityXml.'</Amenities>';
                $body .='<Street>'.$property->address.'</Street>
                    <ZipCode>'.$property->postal_code.'</ZipCode>
                    <Coordinates>
                        <Longitude>28.7041</Longitude>
                        <Latitude>77.1025</Latitude>
                    </Coordinates>
                <Images>';
                foreach($property->images as $image){
                    $body .='<Image ImageTypeID="'.$image->tbl_ru_image_type_id.'" ImageReferenceID="'.$image->id.'">'.env("APP_URL").'/'.$image->filename.'</Image>';
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

                $body .='</ImageSecondaryTypes>
                    <ArrivalInstructions>
                        <Landlord>Incredstays</Landlord>
                        <Email>incredstays@gmail.com</Email>
                        <Phone>9999999999</Phone>
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
                        <PaymentMethod PaymentMethodID="1">Account number: 000000000000000</PaymentMethod>
                        <PaymentMethod PaymentMethodID="2" >VISA, MASTERCARD, AMERICAN EXPRESS</PaymentMethod>
                    </PaymentMethods>
                    <TermsAndConditionsLinks>
                        <TermsAndConditionsLink LanguageID="1">'.env("APP_URL").'</TermsAndConditionsLink>
                    </TermsAndConditionsLinks>
                    <Deposit DepositTypeID="3">'.$property->per_night_price.'</Deposit>';
                    $body .='<CancellationPolicies>';
                    if($cancellationSlab->count()> 0){
                        foreach($cancellationSlab as $cslab){
                            $body .=' <CancellationPolicy ValidFrom="'.$cslab->slab_from.'" ValidTo="'.$cslab->slab_to.'">'.(integer)$cslab->slab.'</CancellationPolicy>';
                        }
                    }
                    else{
                        $body .=' <CancellationPolicy ValidFrom="1" ValidTo="7">40</CancellationPolicy>';
                    }
                    $body .='</CancellationPolicies>';
                    $body .='<Descriptions>
                         <Description LanguageID="1">
                           <Text>'.$property->ru_description.'</Text>
                            <HouseRules>'.strip_tags($property->house_rules).'</HouseRules>
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
                    $otherRuRooms = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('multi_unit_id', $id)->groupBy('ru_room_id')->get();
    
                    foreach($otherRuRooms as $otheroom){
                        $body .='<CompositionRoomAmenities CompositionRoomID="'.$otheroom->ru_room_id.'"><Amenities> <Amenity Count="1">'.$otheroom->ru_room_id.'</Amenity>';
                        $otherRuRoomsAmmenity = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('multi_unit_id', $id)->where('ru_room_id', $otheroom->ru_room_id)->get();
                        foreach($otherRuRoomsAmmenity as $ammenity){
                            $body .='<Amenity Count="1">'.$ammenity->ammenity_id.'</Amenity>';
                        }
                        $body .='</Amenities></CompositionRoomAmenities>';
                    }
                    $body .='</CompositionRoomsAmenities><LicenceInfo>
                        <LicenceNumber>1222233444</LicenceNumber>
                    </LicenceInfo>
                    </Property>
                </Push_PutProperty_RQ>';
                
            $request = new Request('POST', config.ru(''), $headers, $body);
            $res = $client->sendAsync($request)->wait();
            $res->getBody();
            $result = simplexml_load_string($res->getBody());
            $result_json = json_encode($result);
            $result_array = json_decode($result_json,TRUE);

            if(isset($result_array['Status']) ){
                if($result_array['Status']=='Success'){
                    //SELF::updateRoomAmmenitiesMultiUnit($property->id);
                    //SELF::syncMultiUnitAmmenities($property->id);
                    $property->ru_property_id = $result_array['ID'];
                    $property->is_published = 1;
                    $property->save();
                    // $dateFrom  = date('Y-m-d');
                    // $dateTo = date('Y-m-d', strtotime($dateFrom . '+180 day'));
                    // $postAvaliability = "<Push_PutAvbUnits_RQ>
                    //     <Authentication>
                    //         <UserName>".config('ru.RU_USER_NAME')."</UserName>
                    //         <Password>".config('ru.RU_PASSWORD')."</Password>
                    //     </Authentication>
                    //     <MuCalendar PropertyID='".$result_array['ID']."'>
                    //         <Date From='".$dateFrom."' To='".$dateTo."'>
                    //             <U>1</U>
                    //             <C>4</C>
                    //         </Date>
                    //     </MuCalendar>
                    // </Push_PutAvbUnits_RQ>";
                    // $request = new Request('POST', config.ru(''), $headers, $postAvaliability);
                    // $res = $client->sendAsync($request)->wait();
                    // $postprice = "<Push_PutPrices_RQ>
                    //         <Authentication>
                    //         <UserName>".config('ru.RU_USER_NAME')."</UserName>
                    //         <Password>".config('ru.RU_PASSWORD')."</Password>
                    //         </Authentication>
                    //         <Prices PropertyID='".$result_array['ID']."'>
                    //         <Season DateFrom='".$dateFrom."' DateTo='".$dateTo."'>
                    //             <Price>".$property->per_night_price."</Price>
                    //             <Extra>0</Extra>
                    //         </Season>
                    //         </Prices>
                    //     </Push_PutPrices_RQ>";
                    // $request = new Request('POST', config.ru(''), $headers, $postprice);
                    // $res = $client->sendAsync($request)->wait();
                    
                    $controller = new ScriptController();
                    $controller->changeAvaliability($result_array['ID']);
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


    public function syncAmmenities($id){
        $query = TblHome::query();
        $query->with(['additionalCharges', 'images', 'videos', 'assets', 'features', 'reviews', 'amenities', 'ruamenities']);
        $property = $query->find($id);
        

        $location =  TblLocation::where('id', $property->location_id)->first();
        $ruLocation =  TblRuLocation::where('id', $location->ru_location_id)->first();
        $homeType =  TblHomeType::where('id', $property->home_type_id)->first();
        $space = $property->maximum_number_of_guests;
        $parentProperty = TblHome::where('id', $property->home_id)->first();

        $cancellationSlab = DB::table('cancellation_slabs')->where('unit_id', $property->id)->get();
        $homeType = DB::table('tbl_home_types')->where('id', $property->home_type_id)->first();
        $homeAmmeniies = DB::table('tbl_home_amenities')->where('home_id', $property->id)->whereNull('deleted_at')->get();

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

       
        $request = new Request('POST', config.ru(''), $headers, $body);
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


    public function syncUnitAmmenities($id){
     
        $query = TblHomeUnit::query();
        $query->with(['additionalCharges', 'images', 'videos', 'assets', 'features', 'reviews', 'amenities', 'ruamenities']);
        $property = $query->find($id);
        

        $location =  TblLocation::where('id', $property->location_id)->first();
        $ruLocation =  TblRuLocation::where('id', $location->ru_location_id)->first();
        $homeType =  TblHomeType::where('id', $property->home_type_id)->first();
        $space = $property->maximum_number_of_guests;
        $parentProperty = TblHome::where('id', $property->home_id)->first();

       
        $homeAmmeniies = TblHomeAmenities::where('unit_id', $property->id)->get();
     

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
        

       
        $request = new Request('POST', config.ru(''), $headers, $body);
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


    public function syncMultiUnitAmmenities($id){
        $query = TblHomeMultiUnit::query();
        $property = $query->find($id);
        $homeAmmeniies = DB::table('tbl_home_amenities')->where('multi_unit_id', $property->id)->whereNull('deleted_at')->get();
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

       
        $request = new Request('POST', config.ru(''), $headers, $body);
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


    public function updatePropertyInRu($id){

        
        $query = TblHome::query();
        $query->with(['additionalCharges', 'images', 'videos', 'assets', 'features', 'reviews', 'amenities', 'ruamenities']);
        $property = $query->find($id);

        $location =  TblLocation::where('id', $property->location_id)->first();
        $ruLocation =  TblRuLocation::where('id', $location->ru_location_id)->first();
        $homeType =  TblHomeType::where('id', $property->home_type_id)->first();
        $space = $property->maximum_number_of_guests;
        $parentProperty = TblHome::where('id', $property->home_id)->first();

        $cancellationSlab = DB::table('cancellation_slabs')->where('unit_id', $property->id)->get();
        $homeType = DB::table('tbl_home_types')->where('id', $property->home_type_id)->first();

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
                <IsArchived>false</IsArchived>
                <Name>'.$property->home_name.'</Name>
             
                <StandardGuests>'.$space.'</StandardGuests>
                <CanSleepMax>'.$space.'</CanSleepMax>
                <PropertyTypeID>'.$homeType->ru_property_type_id.'</PropertyTypeID>
                <Descriptions>
                    <Description LanguageID="1">
                    <Text>'.$property->ru_description.'</Text>
                   
                    </Description>
                </Descriptions>
                <Street>'.$property->address.'</Street>
                <ZipCode>'.$property->postal_code.'</ZipCode>
                <CheckInOut>
                    <CheckInFrom>'.$property->checkin_time.'</CheckInFrom>
                    <CheckInTo>'.$property->checkout_time.'</CheckInTo>
                    <CheckOutUntil>'.$property->checkout_time.'</CheckOutUntil>
                    <Place>Apartmant</Place>
                </CheckInOut>
            </Property>
        </Push_PutProperty_RQ>';

        $request = new Request('POST', config.ru(''), $headers, $body);
        $res = $client->sendAsync($request)->wait();
        $res->getBody();
        $result = simplexml_load_string($res->getBody());
        $result_json = json_encode($result);
        $result_array = json_decode($result_json,TRUE);

        if(isset($result_array['Status']) ){
            if($result_array['Status']=='Success'){
                $controller = new SyncPropertyInRuPriceController();
                $controller->updatePrice($property->ru_property_id, $property->per_night_price);
                $controller->updateRuMinStay($property->ru_property_id, $property->min_stay);
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


    public function updateUnitInRu($id){
        $query = TblHomeUnit::query();
        $property = $query->find($id);
        $homeType = DB::table('tbl_home_types')->where('id', $property->home_type_id)->first();

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
                <IsArchived>false</IsArchived>
                <Name>'.$property->unit_name.'</Name>
                <StandardGuests>'.$canSleep.'</StandardGuests>
                <CanSleepMax>'.$canSleep.'</CanSleepMax>
                <PropertyTypeID>'.$homeType->ru_property_type_id.'</PropertyTypeID>
                <Descriptions>
                    <Description LanguageID="1">
                    <Text>'.$property->ru_description.'</Text>
                    </Description>
                </Descriptions>
                <Street>'.$property->location.'</Street>
                <ZipCode>'.$property->postal_code.'</ZipCode>
                <CheckInOut>
                    <CheckInFrom>'.$property->checkin_time.'</CheckInFrom>
                    <CheckInTo>'.$property->checkout_time.'</CheckInTo>
                    <CheckOutUntil>'.$property->checkout_time.'</CheckOutUntil>
                    <Place>Apartmant</Place>
                </CheckInOut>
            </Property>
        </Push_PutProperty_RQ>';

        $request = new Request('POST', 'https://rm.rentalsunited.com/api/Handler.ashx', $headers, $body);
        $res = $client->sendAsync($request)->wait();
        $res->getBody();
        $result = simplexml_load_string($res->getBody());
        $result_json = json_encode($result);
        $result_array = json_decode($result_json,TRUE);
        if(isset($result_array['Status']) ){
            if($result_array['Status']=='Success'){
                // $controller = new SyncPropertyInRuPriceController();
                // $controller->updatePrice($property->ru_property_id, $property->per_night_price);
                // $controller->updateRuMinStay($property->ru_property_id, $property->min_stay);
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
                    'message' =>$result_array['Status']
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


    public function updateMultiUnitInRu($id){
        
        $query = TblHomeMultiUnit::query();
        $property = $query->find($id);
        $homeType = DB::table('tbl_home_types')->where('id', $property->home_type_id)->first();

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
                <IsArchived>false</IsArchived>
                <Name>'.$property->unit_name.'</Name>
                <StandardGuests>'.$canSleep.'</StandardGuests>
                <CanSleepMax>'.$canSleep.'</CanSleepMax>
                <PropertyTypeID>'.$homeType->ru_property_type_id.'</PropertyTypeID>
                <Descriptions>
                    <Description LanguageID="1">
                    <Text>'.$property->ru_description.'</Text>
                    
                    </Description>
                </Descriptions>
                <Street>'.$property->address.'</Street>
                <ZipCode>'.$property->postal_code.'</ZipCode>
                <CheckInOut>
                    <CheckInFrom>'.$property->checkin_time.'</CheckInFrom>
                    <CheckInTo>'.$property->checkout_time.'</CheckInTo>
                    <CheckOutUntil>'.$property->checkout_time.'</CheckOutUntil>
                    <Place>Apartmant</Place>
                </CheckInOut>
            </Property>
        </Push_PutProperty_RQ>';

        $request = new Request('POST', config.ru(''), $headers, $body);
        $res = $client->sendAsync($request)->wait();
        $res->getBody();
        $result = simplexml_load_string($res->getBody());
        $result_json = json_encode($result);
        $result_array = json_decode($result_json,TRUE);

      

    
        if(isset($result_array['Status']) ){
            if($result_array['Status']=='Success'){
                // $controller = new SyncPropertyInRuPriceController();
                // $controller->updatePrice($property->ru_property_id, $property->per_night_price);
                // $controller->updateRuMinStay($property->ru_property_id, $property->min_stay);
                
                $controller = new ScriptController();
                $controller->changeAvaliability($result_array['ID']);
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

    public function updateImagesInRu($id){
        $query = TblHome::query();
        $query->with(['images']);
        $property = $query->find($id);
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

        $body .='<Images>';
        foreach($property->images as $image){
            $body .='<Image ImageTypeID="'.$image->tbl_ru_image_type_id.'" ImageReferenceID="'.$image->id.'">'.env("APP_URL").'/storage/home/images/'.$image->filename.'</Image>';
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
        $request = new Request('POST', config.ru(''), $headers, $body);
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


    public function updateUnitImagesInRu($id){
        $query = TblHomeUnit::query();
        $query->with(['images']);
        $property = $query->find($id);
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

        $body .='<Images>';
        foreach($property->images as $image){
           
            $body .='<Image ImageTypeID="'.$image->tbl_ru_image_type_id.'" ImageReferenceID="'.$image->id.'">https://sc.tempsite.in/'.$image->filename.'</Image>';
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


    public function updateMultiUnitImagesInRu($id){
        $query = TblHomeMultiUnit::query();
        $query->with(['images']);
        $property = $query->find($id);
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

        $body .='<Images>';
        foreach($property->images as $image){
            $body .='<Image ImageTypeID="'.$image->tbl_ru_image_type_id.'" ImageReferenceID="'.$image->id.'">'.env("APP_URL").'/'.$image->filename.'</Image>';
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
        $request = new Request('POST', config.ru(''), $headers, $body);
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


    public function updateCancellationSlab($id){
        $query = TblHome::query();
        $query->with(['additionalCharges', 'images', 'videos', 'assets', 'features', 'reviews', 'amenities', 'ruamenities']);
        $property = $query->find($id);
        $cancellationSlab = DB::table('cancellation_slabs')->where('home_id', $property->id)->get();
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

        $body .='<CancellationPolicies>';
                foreach($cancellationSlab as $cslab){
                    $body .=' <CancellationPolicy ValidFrom="'.$cslab->slab_from.'" ValidTo="'.$cslab->slab_to.'">'.(integer)$cslab->slab.'</CancellationPolicy>';
                }
        $body .='</CancellationPolicies>';    
                
        $body    .='</Property>
        </Push_PutProperty_RQ>';
        $request = new Request('POST', config.ru(''), $headers, $body);
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


    public function updateCancellationUnitSlab($id){
        $query = TblHomeUnit::query();
        $query->with(['additionalCharges', 'images', 'videos', 'assets', 'features', 'reviews', 'amenities', 'ruamenities']);
        $property = $query->find($id);
        $cancellationSlab = DB::table('cancellation_slabs')->where('unit_id', $property->id)->get();
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

        $body .='<CancellationPolicies>';
                foreach($cancellationSlab as $cslab){
                    $body .=' <CancellationPolicy ValidFrom="'.$cslab->slab_from.'" ValidTo="'.$cslab->slab_to.'">'.(integer)$cslab->slab.'</CancellationPolicy>';
                }
        $body .='</CancellationPolicies>';    
                
        $body    .='</Property>
        </Push_PutProperty_RQ>';
        $request = new Request('POST', config.ru(''), $headers, $body);
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


    public function updateCancellationMultiUnitSlab($id){
        $query = TblHomeMultiUnit::query();
        $query->with(['additionalCharges', 'images', 'videos', 'assets', 'features', 'reviews', 'amenities', 'ruamenities']);
        $property = $query->find($id);
        $cancellationSlab = DB::table('cancellation_slabs')->where('multi_unit_id', $property->id)->get();
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

        $body .='<CancellationPolicies>';
                foreach($cancellationSlab as $cslab){
                    $body .=' <CancellationPolicy ValidFrom="'.$cslab->slab_from.'" ValidTo="'.$cslab->slab_to.'">'.(integer)$cslab->slab.'</CancellationPolicy>';
                }
        $body .='</CancellationPolicies>';    
                
        $body    .='</Property>
        </Push_PutProperty_RQ>';
        $request = new Request('POST', config.ru(''), $headers, $body);
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


    public function updateUnitCancellationSlab($id){
        $query = TblHomeUnit::query();
        $property = $query->find($id);
        $cancellationSlab = DB::table('cancellation_slabs')->where('unit_id', $property->id)->whereNull('deleted_at')->get();
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

        $body .='<CancellationPolicies>';
                foreach($cancellationSlab as $cslab){
                    $body .=' <CancellationPolicy ValidFrom="'.$cslab->slab_from.'" ValidTo="'.$cslab->slab_to.'">'.(integer)$cslab->slab.'</CancellationPolicy>';
                }
        $body .='</CancellationPolicies>';    
                
        $body    .='</Property>
        </Push_PutProperty_RQ>';
        $request = new Request('POST', config.ru(''), $headers, $body);
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


    public function updateMultiUnitCancellationSlab($id){
        $query = TblHomeMultiUnit::query();
        $property = $query->find($id);
        $cancellationSlab = DB::table('cancellation_slabs')->where('multi_unit_id', $property->id)->whereNull('deleted_at')->get();
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

        $body .='<CancellationPolicies>';
                foreach($cancellationSlab as $cslab){
                    $body .=' <CancellationPolicy ValidFrom="'.$cslab->slab_from.'" ValidTo="'.$cslab->slab_to.'">'.(integer)$cslab->slab.'</CancellationPolicy>';
                }
        $body .='</CancellationPolicies>';    
                
        $body    .='</Property>
        </Push_PutProperty_RQ>';
        $request = new Request('POST', config.ru(''), $headers, $body);
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


   


    public function updateUnitPropertyInRu($id){
        $query = TblHomeUnit::query();
        $query->with(['additionalCharges', 'images', 'videos', 'assets', 'features', 'reviews', 'amenities', 'ruamenities']);
        $property = $query->find($id);

        $location =  TblLocation::where('id', $property->location_id)->first();
        $ruLocation =  TblRuLocation::where('id', $location->ru_location_id)->first();
        $homeType =  TblHomeType::where('id', $property->home_type_id)->first();
        $space = $property->maximum_number_of_guests;
        $parentProperty = TblHome::where('id', $property->home_id)->first();

        $cancellationSlab = DB::table('cancellation_slabs')->where('unit_id', $property->id)->get();
        $homeType = DB::table('tbl_home_types')->where('id', $property->home_type_id)->first();

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
                <IsArchived>false</IsArchived>
                <Name>'.$property->unit_name.'</Name>
                <Space>'.$space.'</Space>
                <StandardGuests>'.$canSleep.'</StandardGuests>
                <CanSleepMax>'.$canSleep.'</CanSleepMax>
                <PropertyTypeID>'.$homeType->ru_property_type_id.'</PropertyTypeID>
                <Descriptions>
                    <Description LanguageID="1">
                    <Text>'.$property->ru_description.'</Text>
                    </Description>
                </Descriptions>
                <Street>'.$property->address.'</Street>
                <ZipCode>'.$property->postal_code.'</ZipCode>
                <CheckInOut>
                    <CheckInFrom>'.$property->checkin_time.'</CheckInFrom>
                    <CheckInTo>'.$property->checkout_time.'</CheckInTo>
                    <CheckOutUntil>'.$property->checkout_time.'</CheckOutUntil>
                    <Place>Apartmant</Place>
                </CheckInOut>
            </Property>
        </Push_PutProperty_RQ>';
        $request = new Request('POST', config.ru(''), $headers, $body);
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

    public function updateRoomAmmenities($id){
     
        $query = TblHome::query();
        $query->with(['additionalCharges', 'images', 'videos', 'assets', 'features', 'reviews', 'amenities', 'ruamenities']);
        $property = $query->find($id);

        $location =  TblLocation::where('id', $property->location_id)->first();
        $ruLocation =  TblRuLocation::where('id', $location->ru_location_id)->first();
        $homeType =  TblHomeType::where('id', $property->home_type_id)->first();
        $space = $property->maximum_number_of_guests;
        $parentProperty = TblHome::where('id', $property->home_id)->first();

        $cancellationSlab = DB::table('cancellation_slabs')->where('unit_id', $property->id)->get();
        $homeType = DB::table('tbl_home_types')->where('id', $property->home_type_id)->first();

        $roomAmmenities = DB::table('tbl_ru_amenity_mappings')->where('home_id', $property->id)->whereNull('deleted_at')->get();


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
        $body  .='</CompositionRoomsAmenities></Property></Push_PutProperty_RQ>';

        $request = new Request('POST', config.ru(''), $headers, $body);
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
    
   public function updateRoomAmmenitiesUnit($id){
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $query = TblHomeUnit::query();
        $property = $query->find($id);

        $location =  TblLocation::where('id', $property->location_id)->first();
        $ruLocation =  TblRuLocation::where('id', $location->ru_location_id)->first();
        $homeType =  TblHomeType::where('id', $property->home_type_id)->first();
        $space = $property->maximum_number_of_guests;
        $parentProperty = TblHome::where('id', $property->home_id)->first();

        $cancellationSlab = DB::table('cancellation_slabs')->where('unit_id', $property->id)->get();
        $homeType = DB::table('tbl_home_types')->where('id', $property->home_type_id)->first();

        $roomAmmenities = DB::table('tbl_ru_amenity_mappings')->where('unit_id', $property->id)->whereNull('deleted_at')->get();

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
        
        $otherRuRooms = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('unit_id', $id)->groupBy('ru_room_id')->get();
        
        foreach($otherRuRooms as $otheroom){
            $body .='<CompositionRoomAmenities CompositionRoomID="'.$otheroom->ru_room_id.'"><Amenities> <Amenity Count="1">'.$otheroom->ru_room_id.'</Amenity>';
            $otherRuRoomsAmmenity = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('unit_id', $id)->where('ru_room_id', $otheroom->ru_room_id)->get();
            foreach($otherRuRoomsAmmenity as $ammenity){
                $body .='<Amenity Count="1">'.$ammenity->ammenity_id.'</Amenity>';
            }
            $body .='</Amenities></CompositionRoomAmenities>';
        }
        
        $body  .='</CompositionRoomsAmenities></Property></Push_PutProperty_RQ>';

        $request = new Request('POST', config.ru(''), $headers, $body);
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

 
 
    public function updateRoomAmmenitiesMultiUnit($id){
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $query = TblHomeMultiUnit::query();
        $property = $query->find($id);

        $location =  TblLocation::where('id', $property->location_id)->first();
        $ruLocation =  TblRuLocation::where('id', $location->ru_location_id)->first();
        $homeType =  TblHomeType::where('id', $property->home_type_id)->first();
        $space = $property->maximum_number_of_guests;
        $parentProperty = TblHome::where('id', $property->home_id)->first();

        $cancellationSlab = DB::table('cancellation_slabs')->where('unit_id', $property->id)->get();
        $homeType = DB::table('tbl_home_types')->where('id', $property->home_type_id)->first();

        $roomAmmenities = DB::table('tbl_ru_amenity_mappings')->where('multi_unit_id', $property->id)->whereNull('deleted_at')->get();

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
        
        $otherRuRooms = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('multi_unit_id', $id)->groupBy('ru_room_id')->get();
        
        foreach($otherRuRooms as $otheroom){
            $body .='<CompositionRoomAmenities CompositionRoomID="'.$otheroom->ru_room_id.'"><Amenities> <Amenity Count="1">'.$otheroom->ru_room_id.'</Amenity>';
            $otherRuRoomsAmmenity = DB::table('tbl_ru_specific_room_ammenity_mappings')->where('multi_unit_id', $id)->where('ru_room_id', $otheroom->ru_room_id)->get();
            foreach($otherRuRoomsAmmenity as $ammenity){
                $body .='<Amenity Count="1">'.$ammenity->ammenity_id.'</Amenity>';
            }
            $body .='</Amenities></CompositionRoomAmenities>';
        }
        $body  .='</CompositionRoomsAmenities></Property></Push_PutProperty_RQ>';
        
   
        $request = new Request('POST', config.ru(''), $headers, $body);
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

    public function syncUpdatePropertyInRuUnit($id){
        $query = TblHomeUnit::query();
        $query->with(['additionalCharges', 'images', 'videos', 'assets', 'features', 'reviews', 'amenities', 'ruamenities']);
        $property = $query->find($id);
        $location =  TblLocation::where('id', $property->location_id)->first();
        $ruLocation =  TblRuLocation::where('id', $location->ru_location_id)->first();
        $homeType =  TblHomeType::where('id', $property->home_type_id)->first();
        $space = $property->maximum_number_of_guests;
        $parentProperty = TblHome::where('id', $property->home_id)->first();
        $homeType = DB::table('tbl_home_types')->where('id', $property->home_type_id)->first();
        $parentHome = TblHome::where('id', $property->home_id)->first();
        
        
        $setting = DB::table('tbl_sitesettings')->first();

    
      
        if(!$ruLocation){
            return response()->json([
                'status' => false,
                'data' =>'',
                'message' => 'Incorrect Location Mapping'
            ], 500);
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
                <PUID BuildingID="-1">1</PUID>
                <Name>'.$property->unit_name.'</Name>
                <OwnerID>'.config("ru.RU_OWNER_ID").'</OwnerID>
                <DetailedLocationID TypeID="'.$ruLocation->ru_location_type_id.'">'.$ruLocation->ru_location_id.'</DetailedLocationID>
                <IsActive>true</IsActive>
                <IsArchived>false</IsArchived>
             
                <StandardGuests>'.$canSleep.'</StandardGuests>
                <CanSleepMax>'.$canSleep.'</CanSleepMax>
                <PropertyTypeID>'.$homeType->ru_property_type_id.'</PropertyTypeID>
                <NoOfUnits>1</NoOfUnits>
               
                <NumberOfFloors>1</NumberOfFloors>';
        $body .='<Street>'.$parentHome->address.'</Street>
                    <ZipCode>'.$parentHome->postal_code.'</ZipCode>
                    <Coordinates>
                        <Longitude>28.7041</Longitude>
                        <Latitude>77.1025</Latitude>
                    </Coordinates>';        
 
                
                    $body .='<ArrivalInstructions>
                        <Landlord>'.$setting->company_name.'</Landlord>
                        <Email>'.$setting->company_mobile_no.'</Email>
                        <Phone>'.$setting->company_mobile_no.'</Phone>
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
                        <PaymentMethod PaymentMethodID="1">'.$setting->account_no.'</PaymentMethod>
                        <PaymentMethod PaymentMethodID="2" >'.$setting->PaymentMethodID.'</PaymentMethod>
                    </PaymentMethods>
                    <TermsAndConditionsLinks>
                        <TermsAndConditionsLink LanguageID="1">'.env("APP_URL").'</TermsAndConditionsLink>
                    </TermsAndConditionsLinks>
                    <Deposit DepositTypeID="3">'.$property->per_night_price.'</Deposit>';
                   
                    $body .='<Descriptions>
                         <Description LanguageID="1">
                           <Text>'.$property->ru_description.'</Text>
                            <HouseRules>'.$property->house_rules.'</HouseRules>
                         </Description>
                       </Descriptions>
                        <LicenceInfo><LicenceNumber>'.$setting->LicenceNumber.'</LicenceNumber>
                    </LicenceInfo>
                    </Property>
                </Push_PutProperty_RQ>';
         
            $request = new Request('POST', config.ru(''), $headers, $body);
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
                        'message' => 'Property updated Successfully.'
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
    
    
    public function syncUpdatePropertyInRuMultiUnit($id){
        $query = TblHomeMultiUnit::query();
        $query->with(['additionalCharges', 'images', 'videos', 'assets', 'features', 'reviews', 'amenities', 'ruamenities']);
        $property = $query->find($id);
        $location =  TblLocation::where('id', $property->location_id)->first();
        $ruLocation =  TblRuLocation::where('id', $location->ru_location_id)->first();
        $homeType =  TblHomeType::where('id', $property->home_type_id)->first();
        $space = $property->maximum_number_of_guests;
        $parentProperty = TblHome::where('id', $property->home_id)->first();
        $homeType = DB::table('tbl_home_types')->where('id', $property->home_type_id)->first();
       
        
        
        $setting = DB::table('tbl_sitesettings')->first();

    
      
        if(!$ruLocation){
            return response()->json([
                'status' => false,
                'data' =>'',
                'message' => 'Incorrect Location Mapping'
            ], 500);
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
                <PUID BuildingID="-1">1</PUID>
                <Name>'.$property->unit_name.'</Name>
                <OwnerID>'.config("ru.RU_OWNER_ID").'</OwnerID>
                <DetailedLocationID TypeID="'.$ruLocation->ru_location_type_id.'">'.$ruLocation->ru_location_id.'</DetailedLocationID>
                <IsActive>true</IsActive>
                <IsArchived>false</IsArchived>
             
                <StandardGuests>'.$canSleep.'</StandardGuests>
                <CanSleepMax>'.$canSleep.'</CanSleepMax>
                <PropertyTypeID>'.$homeType->ru_property_type_id.'</PropertyTypeID>
                <NoOfUnits>1</NoOfUnits>
               
                <NumberOfFloors>1</NumberOfFloors>';
        $body .='<Street>'.$property->address.'</Street>
                    <ZipCode>'.$property->postal_code.'</ZipCode>
                    <Coordinates>
                        <Longitude>28.7041</Longitude>
                        <Latitude>77.1025</Latitude>
                    </Coordinates>';        
 
                
                    $body .='<ArrivalInstructions>
                        <Landlord>'.$setting->company_name.'</Landlord>
                        <Email>'.$setting->company_mobile_no.'</Email>
                        <Phone>'.$setting->company_mobile_no.'</Phone>
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
                        <PaymentMethod PaymentMethodID="1">'.$setting->account_no.'</PaymentMethod>
                        <PaymentMethod PaymentMethodID="2" >'.$setting->PaymentMethodID.'</PaymentMethod>
                    </PaymentMethods>
                    <TermsAndConditionsLinks>
                        <TermsAndConditionsLink LanguageID="1">'.env("APP_URL").'</TermsAndConditionsLink>
                    </TermsAndConditionsLinks>
                    <Deposit DepositTypeID="3">'.$property->per_night_price.'</Deposit>';
                   
                    $body .='<Descriptions>
                         <Description LanguageID="1">
                           <Text>'.$property->ru_description.'</Text>
                            <HouseRules>'.$property->house_rules.'</HouseRules>
                         </Description>
                       </Descriptions>
                        <LicenceInfo><LicenceNumber>'.$setting->LicenceNumber.'</LicenceNumber>
                    </LicenceInfo>
                    </Property>
                </Push_PutProperty_RQ>';
                
           
            $request = new Request('POST', config.ru(''), $headers, $body);
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
                        'message' => 'Property updated Successfully.'
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
    
    
     public function syncUnitAmmenitiefs($id){
     
        $query = TblHomeUnit::query();
        $query->with(['additionalCharges', 'images', 'videos', 'assets', 'features', 'reviews', 'amenities', 'ruamenities']);
        $property = $query->find($id);
        

        $location =  TblLocation::where('id', $property->location_id)->first();
        $ruLocation =  TblRuLocation::where('id', $location->ru_location_id)->first();
        $homeType =  TblHomeType::where('id', $property->home_type_id)->first();
        $space = $property->maximum_number_of_guests;
        $parentProperty = TblHome::where('id', $property->home_id)->first();

       
        $homeAmmeniies = TblHomeAmenities::where('unit_id', $property->id)->get();
     

        $client = new Client();
        $headers = ['Content-Type' => 'application/xml'];
        $canSleep =$property->no_of_bedrooms*2;
        $body = '<Push_PutProperty_RQ>
            <Authentication>
                <UserName>'.config("ru.RU_USER_NAME").'</UserName>
                <Password>'.config("ru.RU_PASSWORD").'</Password>
            </Authentication>
            <Property>
                <ID>'.$home->ru_property_id.'</ID>
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
        


       
        $request = new Request('POST', config.ru(''), $headers, $body);
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