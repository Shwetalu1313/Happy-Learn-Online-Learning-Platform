<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnums;
use App\Models\Jobpost;
use App\Models\SystemActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Validator;

class JobPostController extends Controller
{
    private function getTitle() : string {
        return __('nav.j_post_f');
    }

    private function getTitleUpdate() : string {
        return __('nav.j_post_f_u');
    }

    public function index()
    {
        $titlePage = $this->getTitle();
            return view('job.post', compact('titlePage'));
    }

    private function getAllJobs(){
        return JobPost::all();
    }

    public function list()
    {
        $titlePage = __('nav.j_post_l');
        $jobs = $this->getAllJobs();
        return view('job.list', compact('titlePage', 'jobs'));
    }


    protected function store(Request $request): RedirectResponse
    {

        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'requirements' => 'required|string',
        ]);

        // If validation fails, redirect back with error messages
        if ($validator->fails()) {
            return redirect()->back()->with('error', 'data is begin null');
        }

        $title = $request->input('title');
        $requirements = $request->input('requirements');
        $job = JobPost::create([
            'title' => $title,
            'requirements' => $requirements
        ]);


        if ($job) {

            $systemActivity = [
                'table_name' => Jobpost::getModelName(),
                'ip_address' => $request->getClientIp(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'short' => 'A new job was created. position - ' . $job->title,
                'about' => 'A new job was created. position - ' . $job->title .'by '. auth()->user()->name,
                'target' => UserRoleEnums::ADMIN,
                'route_name' => $request->route()->getName(),
            ];
            SystemActivity::createActivity($systemActivity);

            return redirect()->route('job.show', $job->id)->with('success', 'Job created successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to create job.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($jobpost)
    {
        $titlePage = $this->getTitleUpdate();
        $job = JobPost::findOrFail($jobpost);
        return view('job.show', compact('job','titlePage'));
    }


    public function edit(JobPost $jobpost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string',
            'requirements' => 'required|string',
        ]);

        $job = JobPost::findOrFail($id);
        $job->update($request->only(['title', 'requirements']));

        $systemActivity = [
            'table_name' => Jobpost::getModelName(),
            'ip_address' => $request->getClientIp(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'short' => $job->title . ' position was updated.',
            'about' => $job->title . ' position was updated.',
            'target' => UserRoleEnums::ADMIN,
            'route_name' => $request->route()->getName(),
        ];
        SystemActivity::createActivity($systemActivity);

        return redirect()->back()->with('success', __('jobapplication.job_update_alert'));
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, Request $request)
    {
        // Delete the job post
        $jobpost = JobPost::findOrFail($id);
        $jobpost->delete();
        $systemActivity = [
            'table_name' => Jobpost::getModelName(),
            'ip_address' => $request->getClientIp(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'short' => $jobpost->title . 'position was deleted.',
            'about' => $jobpost->title . 'position was deleted.',
            'target' => UserRoleEnums::ADMIN,
            'route_name' => $request->route()->getName(),
        ];
        SystemActivity::createActivity($systemActivity);
        return redirect()->back()->with('success', 'Deleted successfully.');
    }

    public function joblist(){
        $jobs = $this->getAllJobs();
        return view('job.listV2', compact('jobs'));
    }

    public function jobDetail($id){
        $job = JobPost::findOrFail($id);
        return view('job.detail', compact('job'));
    }

}
