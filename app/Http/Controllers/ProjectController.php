<?php

namespace App\Http\Controllers;

use App\Exports\ProjectExport;
use App\Models\Company;
use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ProjectController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    return view('projects.index', [
      'projects' => Project::latest()->get(),
      'title' => 'Projects'
    ]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('projects.create', [
      'customers' => Company::all(),
      'title' => 'Create Project',
    ]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\StoreProjectRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function store(StoreProjectRequest $request)
  {
    try {
      $validatedData = $request->safe()->all();

      DB::beginTransaction();

      Project::create($validatedData);

      DB::commit();

      return redirect('/projects')->with('success', 'New project has been added!');
    } catch (Exception $e) {
      DB::rollBack();
      return redirect('/projects/create')->withInput()->with('error', $e->getMessage());
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Http\Response
   */
  public function show(Project $project)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Http\Response
   */
  public function edit(Project $project)
  {
    return view('projects.edit', [
      'project' => $project,
      'customers' => Company::all(),
      'title' => "Update Project : {$project->name}"
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\UpdateProjectRequest  $request
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateProjectRequest $request, Project $project)
  {
    try {

      $validatedData = $request->safe()->all();

      DB::beginTransaction();

      Project::where('id', $project->id)->update($validatedData);

      DB::commit();

      return redirect('/projects')->with('success', 'Project has been updated!');
    } catch (Exception $e) {
      DB::rollBack();
      return redirect('/projects')->withInput()->with('error', $e->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Http\Response
   */
  public function destroy(Project $project)
  {
    //
  }

  public function exportExcel()
  {
    $timestamp = now()->timestamp;
    return Excel::download(new ProjectExport, "projects_export_{$timestamp}.xlsx");
  }
}