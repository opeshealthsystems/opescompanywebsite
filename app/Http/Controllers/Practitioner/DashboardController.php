<?php
namespace App\Http\Controllers\Practitioner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load('practitionerProfile');

        return view('practitioner.dashboard', [
            'user'    => $user,
            'profile' => $user->practitionerProfile,
        ]);
    }
}
