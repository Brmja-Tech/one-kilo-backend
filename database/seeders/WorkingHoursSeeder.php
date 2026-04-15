<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkingHoursSeeder extends Seeder
{
    public function run(): void
    {
        $days = [
            [0, ['en' => 'Sunday', 'ar' => 'الاحد'], 'sunday', '10:00:00', '23:00:00', 'open'],
            [1, ['en' => 'Monday', 'ar' => 'الاثنين'], 'monday', '10:00:00', '23:00:00', 'open'],
            [2, ['en' => 'Tuesday', 'ar' => 'الثلاثاء'], 'tuesday', '10:00:00', '23:00:00', 'open'],
            [3, ['en' => 'Wednesday', 'ar' => 'الاربعاء'], 'wednesday', '10:00:00', '23:00:00', 'open'],
            [4, ['en' => 'Thursday', 'ar' => 'الخميس'], 'thursday', '10:00:00', '23:00:00', 'open'],
            [5, ['en' => 'Friday', 'ar' => 'الجمعة'], 'friday', '10:00:00', '23:00:00', 'open'],
            [6, ['en' => 'Saturday', 'ar' => 'السبت'], 'saturday', '10:00:00', '23:00:00', 'open'],
        ];

        foreach ($days as $day) {
            DB::table('working_hours')->updateOrInsert(
                ['day_of_week' => $day[0]],
                [
                    'day_name' => json_encode($day[1]),
                    'open_time' => $day[3],
                    'close_time' => $day[4],
                    'status' => $day[5],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
    }
