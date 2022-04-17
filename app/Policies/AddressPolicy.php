<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AddressPolicy
{
  use HandlesAuthorization;

  /**
   * Determine whether the user can view any models.
   *
   * @param  \App\Models\User  $user
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function viewAny(User $user)
  {
    return in_array($user->role->name, ['admin']);
  }

  /**
   * Determine whether the user can view the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Address  $address
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function view(User $user, Address $address)
  {
    //
  }

  /**
   * Determine whether the user can create models.
   *
   * @param  \App\Models\User  $user
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function create(User $user)
  {
    return in_array($user->role->name, ['admin']);
  }

  /**
   * Determine whether the user can update the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Address  $address
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function update(User $user, Address $address)
  {
    //
  }

  /**
   * Determine whether the user can delete the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Address  $address
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function delete(User $user, Address $address)
  {
    //
  }

  /**
   * Determine whether the user can restore the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Address  $address
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function restore(User $user, Address $address)
  {
    //
  }

  /**
   * Determine whether the user can permanently delete the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Address  $address
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function forceDelete(User $user, Address $address)
  {
    //
  }
}