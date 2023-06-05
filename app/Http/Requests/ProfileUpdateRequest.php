<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'email' => ['email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'user_group' => ['string', 'max:255'],
            'user_tel' => ['string', 'max:13', 'regex:/\A0[-\d]{9,12}\z/'],
        ];
    }
    public function messages(): array
    {
        return [
            'user_tel.max' => '電話番号は正しい形式（0xx-xxxx-xxxx:ハイフンの有無は問いません）で入力してください。',
            'user_tel.regex' => '電話番号は正しい形式（0xx-xxxx-xxxx:ハイフンの有無は問いません）で入力してください。',
           
        ];
    }

}
