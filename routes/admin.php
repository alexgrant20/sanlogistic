<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\PersonController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AddressController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\VehicleChecklistController;
use App\Http\Controllers\Admin\VehicleLastStatusController;

use Illuminate\Support\Facades\Route;


Route::prefix('/admin')->name('admin.')->middleware('auth', 'can:access-admin-panel')->group(function () {

  /**
   * USER
   */
  Route::resource('/users', UserController::class)->except(['index', 'destroy', 'create', 'show']);

  /**
   * ASSIGN USER ROLE & PERMISSION
   */
  Route::get('/users/create/{person}', [UserController::class, 'create'])->name('users.create');
  Route::post('/users/{user}/roles', [UserController::class, 'assignRole'])->name('users.roles');
  Route::delete('/users/{user}/roles/{role}', [UserController::class, 'removeRole'])->name('users.roles.remove');
  Route::post('/users/{user}/permissions', [UserController::class, 'givePermission'])->name('users.permissions');
  Route::delete('/users/{user}/permissions/{permission}', [UserController::class, 'revokePermission'])->name('users.permissions.revoke');

  Route::get('/driver/{driver}/location', [UserController::class, 'editLastLocation'])->name('driver.edit-last-location');
  Route::put('/driver/{driver}/location', [UserController::class, 'updateLastLocation'])->name('driver.update-last-location');

  /**
   * USER ROLES & PERMISSION
   */
  Route::resource('/roles', RoleController::class);
  Route::post('/roles/{role}/permissions', [RoleController::class, 'givePermission'])->name('roles.permissions');
  Route::delete('/roles/{role}/permissions/{permission}', [RoleController::class, 'revokePermission'])->name('roles.permissions.revoke');
  // Route::resource('/permissions', PermissionController::class);
  // Route::post('/permissions/{permission}/roles', [PermissionController::class, 'assignRole'])->name('permissions.roles');
  // Route::delete('/permissions/{permission}/roles/{role}', [PermissionController::class, 'removeRole'])->name('roles.permissions.remove');

  /**
   * ADDRESS
   */
  Route::controller(AddressController::class)->prefix('/addresses')->name('addresses.')->group(function () {
    Route::get('/export/excel', 'exportExcel')->name('export.excel');
    Route::post('/import/excel', 'importExcel')->name('import.excel');
    Route::get('/city/{id}', 'city')->name('cities');
    Route::get('/district/{id}', 'district')->name('districts');
    Route::get('/sub_district/{id}', 'subDistrict')->name('sub-districts');
    Route::get('/location', 'location')->name('locations');
  });

  Route::resource('/addresses', AddressController::class)->except(['show', 'destroy']);

  /**
   * ACTIVITY
   */
  Route::controller(ActivityController::class)->prefix('/activities')->name('activities.')->group(function () {
    Route::post('/export/excel', 'exportExcel')->name('export.excel');
    Route::post('/import/excel', 'importExcel')->name('import.excel');
    Route::get('/showLog/{activity:id}', 'showLog')->name('logs');
    Route::get('/list/{status?}', 'getListIndex')->name('list');
    Route::post('/cancel', 'cancel')->name('cancel');
    Route::get('/approval', 'approval')->name('approval');
  });

  Route::resource('/activities', ActivityController::class)->except('show', 'destroy');

  /**
   * PERSON
   */
  Route::controller(PersonController::class)->prefix('/people')->name('people.')->group(function () {
    Route::get('/export/excel', 'exportExcel')->name('export.excel');
    Route::post('/import/excel', 'importExcel')->name('import.excel');
  });

  Route::resource('/people', PersonController::class)->except('show');

  Route::get('/people/finance', [PersonController::class, 'finance'])->name('people.finance');
  Route::get('/people/finance/list', [PersonController::class, 'getFinanceList'])->name('people.finance.list');

  /**
   * VEHICLE
   */
  Route::controller(VehicleController::class)->prefix('/vehicles')->name('vehicles.')->group(function () {
    Route::get('/export/excel', 'exportExcel')->name('export.excel');
    Route::post('/import/excel', 'importExcel')->name('import.excel');
    Route::get('/migrate/image', 'migrateImage')->name('migrate.image');
    Route::get('/vehicle_type/{id}', 'vehicleType')->name('types');
    Route::get('/vehicle_variety/{id}', 'vehicleVariety')->name('varieties');
  });

  Route::resource('/vehicles', VehicleController::class)->except('show', 'destroy');

  /**
   * VEHICLE CHECKLIST
   */

  Route::controller(VehicleChecklistController::class)->name('vehicles-checklists.')->group(function () {
    Route::get('/vehicle-checklist/{vehicleChecklist}', 'show')->name('show');
  });

  /**
   * VEHICLE LAST STATUS
   */

  Route::controller(VehicleLastStatusController::class)->name('vehicles-last-statuses.')->group(function () {
    Route::get('/vehicle-last-status/{vehicle}', 'show')->name('show');
    Route::get('/vehicle-last-statuses', 'index')->name('index');
  });

  /**
   * COMPANY
   */

  Route::controller(CompanyController::class)->prefix('/companies')->name('companies.')->group(function () {
    Route::get('/export/excel', 'exportExcel')->name('export.excel');
    Route::post('/import/excel', 'importExcel')->name('import.excel');
  });

  Route::resource('/companies', CompanyController::class)->except('show', 'destroy');

  /**
   * PROJECT
   */
  Route::controller(ProjectController::class)->prefix('/projects')->name('projects.')->group(function () {
    Route::prefix('/assign')->group(function () {
      Route::get('/vehicles/{project:name}', 'indexAssignVehicle')->name('show.vehicles');
      Route::get('/people/{project:name}', 'indexAssignPerson')->name('show.people');
      Route::get('/addresses/{project:name}', 'indexAssignAddress')->name('show.addresses');
      Route::post('/vehicles', 'assignVehicle')->name('assign.vehicles');
      Route::post('/people', 'assignPerson')->name('assign.people');
      Route::post('/addresses', 'assignAddress')->name('assign.addresses');
    });
    Route::get('/vehicles', 'vehicles')->name('vehicles');
    Route::get('/addresses', 'address')->name('addresses');
    Route::get('/people', 'people')->name('people');
    Route::get('/export/excel', 'exportExcel')->name('export.excel');
    Route::post('/import/excel', 'importExcel')->name('import.excel');
  });

  Route::resource('/projects', ProjectController::class)->except('show', 'destroy');

  /**
   * FINANCE
   */
  Route::controller(FinanceController::class)->prefix('/finances')->name('finances.')->group(function () {
    Route::post('/approve', 'approve')->name('approve');
    Route::get('/payment', 'payment')->name('payment');
    Route::post('/pay', 'pay')->name('pay');
    Route::post('/reject', 'reject')->name('reject');
    Route::get('/approval/{activity}/edit', 'edit')->name('approval.edit');
    Route::put('/approval/{activity}', 'audit')->name('audit');
    Route::post('/export/excel', 'exportExcel')->name('export.excel');
    Route::post('/export/pdf', 'exportPDF')->name('export.pdf');
  });

  /**
   * Area
   */
  Route::resource('/areas', AreaController::class);


  /**
   * Department
   */
  Route::resource('/departments', DepartmentController::class);
});
