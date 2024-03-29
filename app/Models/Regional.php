<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regional extends Model
{
  use HasFactory;

  protected $guarded = ['id'];

  public function area()
  {
    return $this->hasMany(Area::class);
  }
}