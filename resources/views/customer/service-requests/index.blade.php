<x-layouts.customer title="Service Requests">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Service Requests</h1>
            <p class="text-slate-500 mt-1">Schedule installation, maintenance, or training visits.</p>
        </div>
        <a href="{{ route('customer.service-requests.create', ['locale' => app()->getLocale()]) }}"
           class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">
            + New Request
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif

    @if($serviceRequests->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 p-10 text-center text-slate-400">
            No service requests yet. Schedule a visit from the OPES team.
        </div>
    @else
        <div class="space-y-3">
            @foreach($serviceRequests as $request)
                @php
                    $statusColors = [
                        'pending'   => 'bg-amber-100 text-amber-700',
                        'confirmed' => 'bg-blue-100 text-blue-700',
                        'assigned'  => 'bg-indigo-100 text-indigo-700',
                        'completed' => 'bg-green-100 text-green-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                    ];
                @endphp
                <a href="{{ route('customer.service-requests.show', ['locale' => app()->getLocale(), 'serviceRequest' => $request->id]) }}"
                   class="bg-white rounded-xl border border-slate-200 p-5 flex items-center justify-between hover:border-emerald-300 transition block">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="font-mono text-sm text-slate-500">{{ $request->reference_number }}</span>
                            <span class="text-sm font-medium text-slate-800">{{ \App\Models\ServiceRequest::typeOptions()[$request->type] ?? $request->type }}</span>
                        </div>
                        <p class="text-sm text-slate-500">
                            Preferred: {{ $request->preferred_date->format('d M Y') }}
                            @if($request->confirmed_date) &bull; Confirmed: {{ $request->confirmed_date->format('d M Y') }} @endif
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$request->status] ?? 'bg-slate-100 text-slate-600' }}">
                        {{ \App\Models\ServiceRequest::statusOptions()[$request->status] ?? $request->status }}
                    </span>
                </a>
            @endforeach
        </div>
    @endif
</x-layouts.customer>
