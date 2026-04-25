<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EmployeeAttendanceSampleSeeder extends Seeder
{
    /**
     * Seed 2 employees and sample IN/OUT session pairs for demo/testing.
     */
    public function run(): void
    {
        $branch = Branch::first();

        if (!$branch) {
            $branch = Branch::create([
                'name' => 'Demo Branch',
            ]);
        }

        $employee1 = Employee::updateOrCreate(
            [
                'branch_id' => $branch->id,
                'rfid' => 'EMP1001',
            ],
            [
                'name' => 'Rahim Uddin',
                'designation' => 'Lab Assistant',
                'phone' => '01700000001',
                'salary' => 18000,
                'status' => 'Active',
            ]
        );

        $employee2 = Employee::updateOrCreate(
            [
                'branch_id' => $branch->id,
                'rfid' => 'EMP1002',
            ],
            [
                'name' => 'Karim Hasan',
                'designation' => 'Receptionist',
                'phone' => '01700000002',
                'salary' => 22000,
                'status' => 'Active',
            ]
        );

        $today = Carbon::now('Asia/Dhaka')->toDateString();
        $yesterday = Carbon::now('Asia/Dhaka')->subDay()->toDateString();

        Attendance::whereIn('employee_id', [$employee1->id, $employee2->id])
            ->whereIn('date', [$today, $yesterday])
            ->delete();

        $this->createSessions($employee1->id, $today, [
            ['08:55', '10:30'],
            ['10:45', '12:15'],
            ['13:00', '15:00'],
            ['15:30', '17:10'],
            ['17:30', '19:00'],
        ]);

        $this->createSessions($employee2->id, $today, [
            ['09:10', '11:00'],
            ['11:20', '13:00'],
            ['14:00', '18:30'],
        ]);

        $this->createSessions($employee1->id, $yesterday, [
            ['09:00', '12:00'],
            ['13:00', '17:00'],
        ]);

        $this->createSessions($employee2->id, $yesterday, [
            ['09:30', '12:30'],
            ['13:30', '16:30'],
        ]);
    }

    /**
     * Create attendance rows as session pairs (in_time/out_time).
     *
     * @param array<int, array{0:string,1:string}> $sessions
     */
    private function createSessions(int $employeeId, string $date, array $sessions): void
    {
        foreach ($sessions as $session) {
            $in = Carbon::parse($date . ' ' . $session[0], 'Asia/Dhaka');
            $out = Carbon::parse($date . ' ' . $session[1], 'Asia/Dhaka');

            Attendance::create([
                'employee_id' => $employeeId,
                'fingerprint_data' => (string) $employeeId,
                'mode' => 'standard',
                'hour_slot' => (int) $in->format('G'),
                'date' => $date,
                'in_time' => $in->toDateTimeString(),
                'out_time' => $out->toDateTimeString(),
            ]);
        }
    }
}
