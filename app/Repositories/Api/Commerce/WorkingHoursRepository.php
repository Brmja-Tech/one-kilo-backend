<?php

namespace App\Repositories\Api\Commerce;

use App\Models\WorkingHour;

class WorkingHoursRepository
{


    public function checkStatus()
    {
        $now = now();
        $time = $now->format('H:i:s');
        $day  = $now->dayOfWeek; // 0 - 6

        $working = WorkingHour::where('day_of_week', $day)
            ->where('status', 'open')
            ->where(function ($query) use ($time) {
                $query->where(function ($q) use ($time) {
                    // same-day shift
                    $q->whereColumn('open_time', '<=', 'close_time')
                        ->whereTime('open_time', '<=', $time)
                        ->whereTime('close_time', '>=', $time);
                })->orWhere(function ($q) use ($time) {
                    // overnight shift
                    $q->whereColumn('open_time', '>', 'close_time')
                        ->where(function ($q2) use ($time) {
                            $q2->whereTime('open_time', '<=', $time)
                                ->orWhereTime('close_time', '>=', $time);
                        });
                });
            })
            ->first();
    }

}
