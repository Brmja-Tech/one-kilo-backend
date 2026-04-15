<?php

namespace App\Livewire\Dashboard\Settings\Faqs;

use App\Models\Faq;
use Livewire\Component;
use CodeZero\UniqueTranslation\UniqueTranslationRule;

class FaqsCreate extends Component
{
    public $question_ar, $question_en, $answer_ar, $answer_en, $status = 1;


    public function rules()
    {
        return [
            'question_ar' => [
                'required',
                'string',
                'min:4',
                'max:200',
                UniqueTranslationRule::for('faqs', 'question')
            ],
            'question_en' => [
                'required',
                'string',
                'min:4',
                'max:200',
                UniqueTranslationRule::for('faqs', 'question')
            ],

            'answer_ar'          => 'required|string|min:4|',
            'answer_en'          => 'required|string|min:4|',
            'status'             => 'required|in:0,1',
        ];
    }

    public function submit()
    {
        $data = $this->validate();
        // save data in DB
        $data['status'] = $this->status;
        $data['question']  = [
            'ar' => $this->question_ar,
            'en' => $this->question_en,
        ];
        $data['answer']   = [
            'ar' => $this->answer_ar,
            'en' => $this->answer_en,
        ];
        $faq = Faq::create($data);
        $this->reset('question_ar', 'question_en', 'answer_ar', 'answer_en');
        $this->dispatch('faqAddMS');
        //hide modal
        $this->dispatch('createModalToggle');
        //refresh blog data in component
        $this->dispatch('refreshData')->to(FaqsData::class);
    }
    public function render()
    {
        return view('dashboard.settings.faqs.faqs-create');
    }
}
