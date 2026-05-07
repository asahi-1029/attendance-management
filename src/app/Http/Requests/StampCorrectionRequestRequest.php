<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StampCorrectionRequestRequest extends FormRequest
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
            'clock_in' => ['required', 'date_format:H:i'],
            'clock_out' => ['required','date_format:H:i','after:clock_in'],
            'breaks.*.start_time' => ['nullable', 'required_with:breaks.*.end_time', 'date_format:H:i'],
            'breaks.*.end_time' => ['nullable', 'required_with:breaks.*.start_time', 'date_format:H:i'],
            'note' => ['required', 'max:255']
        ];
    }

    public function messages()
    {
        return [
            'clock_in.required' => '出勤時間を入力してください',
            'clock_in.date_format' => '出勤時間は「HH:MM」形式で入力してください',

            'clock_out.required' => '退勤時間を入力してください',
            'clock_out.date_format' => '退勤時間は「HH:MM」形式で入力してください',
            'clock_out.after' => '出勤時間もしくは退勤時間が不適切な値です',

            'breaks.*.start_time.required_with' => '休憩終了を入力する場合は休憩開始も必要です',
            'breaks.*.start_time.date_format' => '休憩開始は「HH:MM」形式で入力してください',

            'breaks.*.end_time.required_with' => '休憩開始を入力する場合は休憩終了も必要です',
            'breaks.*.end_time.date_format' => '休憩終了は「HH:MM」形式で入力してください',

            'note.required' => '備考を記入してください',
            'note.max' => '備考は255文字以内で入力してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $clockIn = $this->clock_in;
            $clockOut = $this->clock_out;

            foreach ($this->breaks ?? [] as $index => $break) {

                $start = $break['start_time'] ?? null;
                $end = $break['end_time'] ?? null;

                // ② 休憩開始が勤務時間外
                if ($start && ($start < $clockIn || $start > $clockOut)) {
                    $validator->errors()->add(
                        "breaks.$index.start_time",
                        '休憩時間が不適切な値です'
                    );
                }

                // ③ 休憩終了が退勤より後
                if ($end && $end > $clockOut) {
                    $validator->errors()->add(
                        "breaks.$index.end_time",
                        '休憩時間もしくは退勤時間が不適切な値です'
                    );
                }

                // 終了が開始より前
                if ($start && $end && $end <= $start) {
                    $validator->errors()->add(
                        "breaks.$index.end_time",
                        '休憩終了は休憩開始より後にしてください'
                    );
                }
            }
        });
    }
}
