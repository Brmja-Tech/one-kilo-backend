<?php

namespace App\Livewire\Dashboard\Settings\WorkingHours;


use App\Models\WorkingHour;
use Livewire\Component;
use CodeZero\UniqueTranslation\UniqueTranslationRule;

class WorkingHoursUpdate extends Component
{
    public $workingHours, $day_name_ar, $day_name_en, $open_time, $close_time, $status = 1;
    protected $listeners = ['workingHoursUpdate'];


    public function workingHoursUpdate($id)
    {
        $this->workingHours               = WorkingHour::find($id);
        $this->day_name_ar       = $this->workingHours->getTranslation('day_name', 'ar');
        $this->day_name_en       = $this->workingHours->getTranslation('day_name', 'en');
        $this->open_time         = $this->workingHours->open_time;
        $this->close_time         = $this->workingHours->close_time;
        $this->status            = $this->workingHours->status;
        $this->dispatch('updateModalToggle');
    }


    public function rules()
    {
        return [
            'day_name_ar' => [
                'required',
                'string',
                'min:5',
                UniqueTranslationRule::for('workingHours', 'day_name')->ignore($this->workingHours->id),
            ],
            'day_name_en' => [
                'required',
                'string',
                'min:5',
                UniqueTranslationRule::for('workingHours', 'day_name')->ignore($this->workingHours->id),
            ],

            'open_time'          => 'required|date_format:H:i',
            'close_time'          => 'required|date_format:H:i',
            'status'             => 'required|in:open,close,busy',
        ];
    }




    public function submit()
    {
        $data = $this->validate();
        // save data in DB
        $data['status'] = $this->status;
        $data['open_time'] = $this->open_time;
        $data['close_time'] = $this->close_time;
        $data['day_name']  = [
            'ar' => $this->day_name_ar,
            'en' => $this->day_name_en,
        ];
        $this->WorkingHours->update($data);
        // Hide modal
        $this->dispatch('workingHoursUpdateMS');
        // Reset form fields
        $this->reset(['day_name_ar', 'day_name_en', 'open_time', 'close_time', 'status']);
        // Dispatch events
        $this->dispatch('updateModalToggle');
        $this->dispatch('refreshData')->to(WorkingHoursData::class);
    }
    public function render()
    {
        return view('dashboard.settings.workingHours.workingHours-update');
    }
}
