<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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
      'address_type_id' => 'required|integer',
      'area_id' => 'required|integer',
      'subdistrict_id' => 'required|integer',
      'district_id' => 'required|integer',
      'city_id' => 'required|integer',
      'province_id' => 'required|integer',
      'pool_type_id' => 'required|integer',
      'name' => "required|string|unique:addresses,name,{$this->address->id}",
      'full_address' => 'required|string',
      'longitude' => 'required|string',
      'latitude' => 'required|string',
      'post_number' => 'required|string'
    ];
  }
}