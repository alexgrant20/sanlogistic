<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityRequest extends FormRequest
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
			'user_id' => 'nullable',
			'vehicle_id' => 'nullable',
			'project_id' => 'nullable',
			'departure_location_id' => 'nullable',
			'arrival_location_id' => 'nullable',
			'do_date' => 'nullable',
			'do_number' => 'nullable',
			'do_image' => 'nullable|image',
			'departure_date' => 'nullable',
			'departure_odo' => 'nullable',
			'departure_odo_image' => 'nullable|image',
			'arrival_date' => 'nullable',
			'arrival_odo' => 'nullable',
			'arrival_odo_image' => 'nullable|image',
			'bbm_amount' => 'nullable',
			'bbm_image' => 'nullable|image',
			'toll_amount' => 'nullable',
			'toll_image' => 'nullable|image',
			'retribution_amount' => 'nullable',
			'retribution_image' => 'nullable|image',
			'parking' => 'nullable',
			'parking_image' => 'nullable|image',
			'status' => 'nullable',
			'type' => 'nullable',
		];
	}
}