<x-layouts.customer title="Service Request {{ $serviceRequest->reference_number }}">
    <div class="mb-6">
        <a href="{{ route('customer.service-requests', ['locale' => app()->getLocale()]) }}"
           class="text-sm text-emerald-600 hover:underline">← Back to Service Requests</a>
        <div class="flex items-center gap-3 mt-2">
            <h1 class="text-2xl font-bold text-slate-900">{{ $serviceRequest->reference_number }}</h1>
            @php
                $statusColors = [
                    'pending'   => 'bg-amber-100 text-amber-700',
                    'confirmed' => 'bg-blue-100 text-blue-700',
                    'assigned'  => 'bg-indigo-100 text-indigo-700',
                    'completed' => 'bg-green-100 text-green-700',
                    'cancelled' => 'bg-red-100 text-red-700',
                ];
            @endphp
            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$serviceRequest->status] ?? 'bg-slate-100 text-slate-600' }}">
                {{ \App\Models\ServiceRequest::statusOptions()[$serviceRequest->status] ?? $serviceRequest->status }}
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 divide-y divide-slate-100">
        <div class="p-5 grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Service Type</p>
                <p class="text-sm font-medium text-slate-800">{{ \App\Models\ServiceRequest::typeOptions()[$serviceRequest->type] ?? $serviceRequest->type }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Preferred Date</p>
                <p class="text-sm font-medium text-slate-800">{{ $serviceRequest->preferred_date->format('d M Y') }}
                    @if($serviceRequest->preferred_time) at {{ $serviceRequest->preferred_time }} @endif
                </p>
            </div>
            @if($serviceRequest->confirmed_date)
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Confirmed Date</p>
                <p class="text-sm font-medium text-emerald-700">{{ $serviceRequest->confirmed_date->format('d M Y') }}
                    @if($serviceRequest->confirmed_time) at {{ $serviceRequest->confirmed_time }} @endif
                </p>
            </div>
            @endif
            @if($serviceRequest->location)
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Location</p>
                <p class="text-sm font-medium text-slate-800">{{ $serviceRequest->location }}</p>
            </div>
            @endif
        </div>

        @if($serviceRequest->description)
        <div class="p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-2">Description</p>
            <p class="text-sm text-slate-700">{{ $serviceRequest->description }}</p>
        </div>
        @endif

        @if($serviceRequest->admin_notes && in_array($serviceRequest->status, ['confirmed','assigned','completed']))
        <div class="p-5 bg-blue-50">
            <p class="text-xs text-blue-500 uppercase tracking-wide mb-2">Notes from OPES</p>
            <p class="text-sm text-blue-800">{{ $serviceRequest->admin_notes }}</p>
        </div>
        @endif
    </div>
</x-layouts.customer>
