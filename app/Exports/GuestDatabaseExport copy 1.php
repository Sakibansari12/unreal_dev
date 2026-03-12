<?php
namespace App\Exports;

use App\Models\BookingGuestId;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\Auth;
use DB;

class GuestDatabaseExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting{
    protected $search_name;
    protected $search_email;
    protected $search_mobile;

    public function __construct(array $data)
    {
        $this->search_name = $data['search_name'] ?? null;
        $this->search_email = $data['search_email'] ?? null;
        $this->search_mobile = $data['search_mobile'] ?? null;
    }

    public function collection(){
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");
        $user = Auth::guard('admin')->user();
        $query = BookingGuestId::query();
        if ($this->search_name) {
            $query->where('name', 'like', '%' . $this->search_name . '%');
        }
        if ($this->search_email) {
            $query->where('email', 'like', '%' . $this->search_email . '%');
        }
        if ($this->search_mobile) {
            $query->where('mobile_no', 'like', '%' . $this->search_mobile . '%');
        }
        if ($user->role_id != 1) {
            $query->where('user_id', $user->id);
        }
        return $query->groupBy('mobile_no')->orderBy('id', 'ASC')->get();
    }

    public function map($guestdatabase): array{
        return [
            '' . ($guestdatabase->name ?? ''),
            '' . ($guestdatabase->email ?? ''),
            !empty($guestdatabase->mobile_no)
                ? (
                    !empty($guestdatabase->country_code)
                        ? '+' . $guestdatabase->country_code . ' ' . $guestdatabase->mobile_no
                        : $guestdatabase->mobile_no
                )
                : '',
        ];
    }

    public function headings(): array
    {
        return [
            'NAME',
            'EMAIL',
            'PHONE NUMBER',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
        ];
    }
}