<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name'=>'required|string',
            'last_name'=>'required|string',
            'email'=>['required', 'email', Rule::unique('users')->ignore($this->user)],
            'image' => 'image|max:1024',
            'password'=>'sometimes|required|confirmed',
            'permissions'=>'required|min:1'

        ];
    }
}
