<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LicenseController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $licenses = License::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('customer.licenses.index', compact('licenses'));
    }

    public function show(Request $request)
    {
        $id      = (int) $request->route('id');
        $user    = Auth::user();
        $license = License::findOrFail($id);

        abort_if((int) $license->user_id !== $user->id, 403);

        return view('customer.licenses.show', compact('license'));
    }
}
