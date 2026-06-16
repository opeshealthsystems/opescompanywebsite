<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $serviceRequests = auth()->user()->serviceRequests()->latest()->get();
        return view('customer.service-requests.index', compact('serviceRequests'));
    }

    public function create()
    {
        $typeOptions = ServiceRequest::typeOptions();
        return view('customer.service-requests.create', compact('typeOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'           => 'required|string|in:' . implode(',', array_keys(ServiceRequest::typeOptions())),
            'product_slug'   => 'nullable|string|max:100',
            'description'    => 'nullable|string|max:1000',
            'preferred_date' => 'required|date|after:today',
            'preferred_time' => 'nullable|string|max:10',
            'location'       => 'nullable|string|max:200',
        ]);

        auth()->user()->serviceRequests()->create($data);

        return redirect()
            ->route('customer.service-requests', ['locale' => app()->getLocale()])
            ->with('success', 'Your service request has been submitted. We will contact you to confirm.');
    }

    public function show($locale, ServiceRequest $serviceRequest)
    {
        abort_unless($serviceRequest->customer_id === auth()->id(), 403);
        return view('customer.service-requests.show', compact('serviceRequest'));
    }
}
