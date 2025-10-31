<?php

namespace App\Imports;

use App\Models\PhoneNumber;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;

class PhoneNumbersImport implements ToCollection
{
    protected $categoryId;

    public function __construct($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header row

            $name = $row[2] ?? 'No Name';
            $number = formatPhoneNumber($row[3]);
            $address1 = ($row[0]);
            $address2 = ($row[1]);
            // Skip if number is invalid (e.g. null)
            if (!$number) {
                continue;
            }

            if (!PhoneNumber::where('number', $number)
                ->where('branch_id', Auth::user()->branch_id)
                ->exists()) {

                PhoneNumber::create([
                    'branch_id' => Auth::user()->branch_id,
                    'number_category_id' => $this->categoryId,
                    'name' => $name,
                    'address' => $address1.' '.$address2,
                    'number' => $number,
                ]);
            }
        }
    }


}
