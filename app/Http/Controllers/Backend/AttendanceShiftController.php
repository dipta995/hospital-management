<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceShift;
use App\Services\AttendanceSchemaService;
use Illuminate\Http\Request;

class AttendanceShiftController extends Controller
{
    public function __construct()
    {
        $this->checkGuard();
    }

    public function index()
    {
        $this->checkOwnPermission('employees.index');

        $installed = AttendanceSchemaService::hasShifts();
        $shifts = $installed
            ? AttendanceShift::where('branch_id', auth()->user()->branch_id)
                ->withCount('employees')
                ->orderByDesc('is_active')
                ->orderBy('start_time')
                ->get()
            : collect();

        return view('backend.pages.attendance.shifts', compact('installed', 'shifts'));
    }

    public function store(Request $request)
    {
        $this->checkOwnPermission('employees.edit');
        $this->ensureInstalled();

        $shift = new AttendanceShift(['branch_id' => auth()->user()->branch_id]);
        $this->fill($shift, $request);
        $shift->save();

        return back()->with('success', 'Shift "' . $shift->name . '" created.');
    }

    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('employees.edit');
        $this->ensureInstalled();

        $shift = AttendanceShift::where('branch_id', auth()->user()->branch_id)->findOrFail($id);
        $this->fill($shift, $request);
        $shift->save();

        return back()->with('success', 'Shift "' . $shift->name . '" updated.');
    }

    public function destroy($id)
    {
        $this->checkOwnPermission('employees.edit');
        $this->ensureInstalled();

        $shift = AttendanceShift::where('branch_id', auth()->user()->branch_id)->findOrFail($id);

        if ($shift->employees()->exists() || Attendance::where('shift_id', $shift->id)->exists()) {
            $shift->is_active = false;
            $shift->save();

            return back()->with('success', 'Shift "' . $shift->name . '" is in use, so it was deactivated instead of deleted.');
        }

        $shift->delete();

        return back()->with('success', 'Shift deleted.');
    }

    private function fill(AttendanceShift $shift, Request $request): void
    {
        $flexible = $request->input('type') === 'flexible';

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:fixed,flexible',
            'start_time' => $flexible ? 'nullable' : 'required|date_format:H:i',
            'end_time' => $flexible ? 'nullable' : 'required|date_format:H:i|different:start_time',
            'break_minutes' => 'nullable|integer|min:0|max:240',
            'flexible_hours' => $flexible ? 'required|numeric|min:1|max:24' : 'nullable',
            'is_active' => 'nullable|boolean',
        ]);

        $shift->name = $data['name'];
        $shift->is_flexible = $flexible;
        $shift->start_time = $flexible ? null : $data['start_time'];
        $shift->end_time = $flexible ? null : $data['end_time'];
        $shift->break_minutes = $flexible ? 0 : (int) ($data['break_minutes'] ?? 0);
        $shift->flexible_hours = $flexible ? $data['flexible_hours'] : null;
        $shift->is_active = $request->boolean('is_active', true);
    }

    private function ensureInstalled(): void
    {
        abort_unless(
            AttendanceSchemaService::hasShifts(),
            503,
            'Attendance Shifts is not installed. Install it from Dashboard → System Updates.'
        );
    }
}
