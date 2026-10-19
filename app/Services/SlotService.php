<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SlotService
{
    /**
     * Doctors available for a department on a given date (have schedule for that weekday).
     */
    public function getDoctorsForDepartment(string $department, string $date): Collection
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        return User::doctors()
            ->where('specialization', $department)
            ->whereHas('schedules', function ($query) use ($dayOfWeek) {
                $query->where('day_of_week', $dayOfWeek)->where('is_active', true);
            })
            ->with(['schedules' => function ($query) use ($dayOfWeek) {
                $query->where('day_of_week', $dayOfWeek)->where('is_active', true);
            }])
            ->orderBy('name')
            ->get();
    }

    /**
     * Available time slots for a doctor on a given date.
     */
    public function getAvailableSlots(int $doctorId, string $date): array
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $schedule = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$schedule) {
            return [];
        }

        $slotDuration = $schedule->slot_duration_minutes;
        $start = Carbon::parse($date . ' ' . $schedule->start_time);
        $end = Carbon::parse($date . ' ' . $schedule->end_time);

        $bookedTimes = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->whereIn('status', ['scheduled', 'checked_in', 'completed'])
            ->pluck('slot_time')
            ->map(fn ($time) => Carbon::parse($time)->format('H:i'))
            ->toArray();

        $slots = [];
        $current = $start->copy();
        $now = Carbon::now();

        while ($current->copy()->addMinutes($slotDuration)->lte($end)) {
            $slotTime = $current->format('H:i');
            $slotEnd = $current->copy()->addMinutes($slotDuration);

            $isPast = Carbon::parse($date)->isToday() && $current->lt($now);
            $isBooked = in_array($slotTime, $bookedTimes, true);

            $slots[] = [
                'time' => $slotTime,
                'display' => $current->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),
                'duration_minutes' => $slotDuration,
                'available' => !$isPast && !$isBooked,
            ];

            $current->addMinutes($slotDuration);
        }

        return $slots;
    }
}
