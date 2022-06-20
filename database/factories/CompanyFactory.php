<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition()
  {
    return [
      'company_type_id' => $this->faker->numberBetween(1, 5),
      'name' => $this->faker->unique()->company(),
      'phone_number' =>  $this->faker->randomNumber(10, true),
      'email' => $this->faker->email(),
      'note' => '',
      'website' => $this->faker->url(),
      'director' => $this->faker->name(),
      'npwp' => $this->faker->randomNumber(10, true),
      'fax' =>  $this->faker->randomNumber(10, true),
      'city_id' => City::factory(),
      'full_address' => $this->faker->address(),
    ];
  }
}