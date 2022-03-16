<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonDocument extends Model
{
  use HasFactory;

  protected $table = 'people_documents';

  protected $guarded = ['id'];

  public function person()
  {
    return $this->belongsTo(Person::class);
  }
}