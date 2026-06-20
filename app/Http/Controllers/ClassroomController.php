<?php

namespace App\Http\Controllers;

use App\Jobs\SyncGoogleClassroomJob;
use App\Models\GoogleClassroomCourse;
use App\Models\GoogleClassroomCourseWork;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassroomController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $courses = GoogleClassroomCourse::ownedBy($user)->active()->get();

        $courseWorks = GoogleClassroomCourseWork::ownedBy($user)
            ->with('course')
            ->orderBy('due_date', 'desc')
            ->get();

        return view('classroom.index', compact('courses', 'courseWorks'));
    }

    public function show(Request $request, GoogleClassroomCourse $course): View
    {
        abort_unless($course->user_id === $request->user()->id, 403);

        $course->load('courseWorks.submissions');

        return view('classroom.show', ['course' => $course]);
    }

    public function sync(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasClassroomAccess()) {
            return back()->with('error', 'Google Classroom belum terhubung.');
        }

        SyncGoogleClassroomJob::dispatch($user);

        return back()->with('success', 'Sinkronisasi Classroom dijadwalkan.');
    }

    public function disconnect(Request $request): RedirectResponse
    {
        $user = $request->user();
        $account = $user->googleAccount;

        if ($account) {
            $account->update(['classroom_connected_at' => null]);
            GoogleClassroomCourse::where('user_id', $user->id)->delete();
        }

        return back()->with('success', 'Google Classroom berhasil diputuskan.');
    }
}
