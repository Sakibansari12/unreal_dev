<?php

namespace App\Exports;

use App\Models\TblLead;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Services\PropertyService;
use Carbon\Carbon;
class LeadListExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;
    protected $propertyService;
    public function __construct($request)
    {
        $this->request = $request;
         $this->propertyService = new PropertyService();
    }

    public function collection()
    {
          $request = (object) $this->request;
          
       // Date filter
        $start_date = null;
        $end_date = null;
        if (!empty($request->searchDateRange) && str_contains($request->searchDateRange, ' to ')) {
            [$from, $to] = explode(' to ', $request->searchDateRange);
            try {
                $start_date = \Carbon\Carbon::createFromFormat('d/m/Y', trim($from))->format('Y-m-d');
                $end_date = \Carbon\Carbon::createFromFormat('d/m/Y', trim($to))->format('Y-m-d');
            } catch (\Exception $e) {
                $start_date = null;
                $end_date = null;
            }
        }
        
        $start_date_in = null;
        $end_date_in = null;
        if (!empty($request->searchDateCheckIn) && str_contains($request->searchDateCheckIn, ' to ')) {
            [$from, $to] = explode(' to ', $request->searchDateCheckIn);
            try {
                $start_date_in = \Carbon\Carbon::createFromFormat('d/m/Y', trim($from))->format('Y-m-d');
                $end_date_in = \Carbon\Carbon::createFromFormat('d/m/Y', trim($to))->format('Y-m-d');
            } catch (\Exception $e) {
                $start_date_in = null;
                $end_date_in = null;
            }
        }


       // $query = TblLead::with(['user']);
        $query = $this->propertyService->applyUserRoleFilter(TblLead::with(['user']));
        if (!empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

         if (!empty($request->stage)) {
            $query->where('stage', $request->stage);
        }
    //     if (!empty($request->checkInDate) && !empty($request->checkOutDate)) {
    //     $query->where('checkin_date', '>=', $request->checkInDate)
    //           ->where('checkout_date', '<=', $request->checkOutDate);
    // }
    
        if ($start_date_in && $end_date_in) {
            $query->whereBetween('checkin_date', [$start_date_in, $end_date_in]);
        }

         // Date filter
        if ($start_date && $end_date) {
            $query->whereBetween('date', [$start_date, $end_date]);
        }

        return $query->orderBy('id', 'desc')->get(); 
    }

    public function headings(): array
    {
        return [
            'Date',
            'Added By',
            'Name',
            'Email',
            'Mobile',
            'Check in',
            'Check out',
            'Stage',
            'Booking ID',
            'Source',
            'Note',
        ];
    }

    public function map($lead): array
    {
        return [
            \Carbon\Carbon::parse($lead->date)->format('d M, Y'),
            $lead->user->name ?? '',
            $lead->name,
            $lead->email,
            $lead->mobile,
            $lead->checkin_date 
                ? Carbon::parse($lead->checkin_date)->format('d M, Y') 
                : '',

            $lead->checkout_date 
                ? Carbon::parse($lead->checkout_date)->format('d M, Y') 
                : '',
            $lead->stage,
            $lead->booking_id ?? '',
            $lead->source ?? '',
            $lead->note ?? '',
            
        ];
    }
}