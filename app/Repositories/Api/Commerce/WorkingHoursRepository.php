<?php

namespace App\Repositories\Api\Commerce;

use App\Models\WorkingHour;

class WorkingHoursRepository
{


    public function checkStatus()
    {
        $now = now('Africa/Cairo');
        $time = $now->format('H:i:s');

      //  dd($time);
        $day  = $now->dayOfWeek; // 0 - 6

       // dd($day);

        $working = WorkingHour::query()
            ->where('status', 'open')
            ->where(function ($query) use ($day, $time) {
                // Carbon's dayOfWeek: 0 (Sunday) - 6 (Saturday)
                $yesterday = ($day + 6) % 7;

                // Scenario 1: Open based on Today's regular shift
                $query->where(function ($q) use ($day, $time) {
                    $q->where('day_of_week', $day)
                        ->whereColumn('open_time', '<=', 'close_time')
                        ->where('open_time', '<=', $time)
                        ->where('close_time', '>=', $time);
                })
                // Scenario 2: Open based on Today's cross-midnight shift (from open_time until midnight tonight)
                ->orWhere(function ($q) use ($day, $time) {
                    $q->where('day_of_week', $day)
                        ->whereColumn('open_time', '>', 'close_time')
                        ->where('open_time', '<=', $time);
                })
                // Scenario 3: Open based on Yesterday's cross-midnight shift (from midnight until yesterday's close_time today)
                ->orWhere(function ($q) use ($yesterday, $time) {
                    $q->where('day_of_week', $yesterday)
                        ->whereColumn('open_time', '>', 'close_time')
                        ->where('close_time', '>=', $time);
                });
            })
            ->first();

        return $working;
    }

}
