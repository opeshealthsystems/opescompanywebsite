<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $documents = Document::where('addressee_user_id', $user->id)
            ->whereNotIn('status', ['draft'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('customer.documents.index', compact('documents'));
    }

    public function show(Request $request)
    {
        $id       = (int) $request->route('id');
        $user     = Auth::user();
        $document = Document::findOrFail($id);

        abort_if($document->addressee_user_id !== $user->id, 403);
        abort_if(in_array($document->status, ['draft']), 403);

        return view('customer.documents.show', compact('document'));
    }
}
