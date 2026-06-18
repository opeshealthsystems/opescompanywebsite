<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $dept = Department::where('head_id', $user->id)->first();

        $query = User::query()
            ->when($dept, fn ($q) => $q->where('department_id', $dept->id))
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%'.$request->search.'%'));

        $team = $query->paginate(20)->withQueryString();

        return view('manager.team.index', compact('team', 'dept'));
    }
}
