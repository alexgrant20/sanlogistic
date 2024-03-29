<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
  use HasFactory;

  protected $guarded = ['id'];

  public function regional()
  {
    return $this->belongsTo(Regional::class);
  }
}