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
            ->where('day_of_week', $day)
            ->where('status', 'open')
            ->where(function ($query) use ($time) {
                $query->where(function ($q) use ($time) {
                    $q->whereColumn('open_time', '<=', 'close_time')
                        ->where('open_time', '<=', $time)
                        ->where('close_time', '>=', $time);
                })->orWhere(function ($q) use ($time) {
                    $q->whereColumn('open_time', '>', 'close_time')
                        ->where(function ($q2) use ($time) {
                            $q2->where('open_time', '<=', $time)
                                ->orWhere('close_time', '>=', $time);
                        });
                });
            })
            ->first();

        return $working;
    }

}
