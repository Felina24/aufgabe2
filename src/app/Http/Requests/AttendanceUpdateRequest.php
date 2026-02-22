<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'clock_in'  => ['required'],
            'clock_out' => ['required'],
            'note'      => ['required'],

            'breaks.*.start' => ['nullable'],
            'breaks.*.end'   => ['nullable'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $clockIn  = $this->clock_in;
            $clockOut = $this->clock_out;

            if ($clockIn && $clockOut && $clockIn >= $clockOut) {
                $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です');
            }

            if (!empty($this->breaks)) {
                foreach ($this->breaks as $index => $break) {

                    $start = $break['start'] ?? null;
                    $end   = $break['end'] ?? null;

                    if ($start && $clockIn && $start < $clockIn) {
                        $validator->errors()->add("breaks.$index.start", '休憩時間が不適切な値です');
                    }

                    if ($start && $clockOut && $start > $clockOut) {
                        $validator->errors()->add("breaks.$index.start", '休憩時間が不適切な値です');
                    }

                    if ($end && $clockOut && $end > $clockOut) {
                        $validator->errors()->add("breaks.$index.end", '休憩時間もしくは退勤時間が不適切な値です');
                    }
                }
            }
        });
    }

    public function messages()
    {
        return [
            'clock_in.required' => '出勤時間を入力してください',
            'clock_out.required' => '退勤時間を入力してください',
            'note.required' => '備考を記入してください',
        ];
    }

}
