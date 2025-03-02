<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityStatus extends Model
{
  use HasFactory, Blameable;

  protected $guarded = ['id'];

  protected $with = ['activityPayment'];

  public function activity()
  {
    return $this->belongsTo(Activity::class);
  }

  public function created_user()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function activityPayment()
  {
    return $this->hasOne(ActivityPayment::class);
  }
}
