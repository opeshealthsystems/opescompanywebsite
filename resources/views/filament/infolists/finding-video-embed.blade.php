@php($embed = $getRecord()?->embedUrl())
<div>
    @if($embed)
        <div style="position:relative;width:100%;max-width:640px;padding-top:56.25%;border-radius:8px;overflow:hidden">
            <iframe src="{{ $embed }}" allowfullscreen
                    allow="accelerated-destination; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    style="position:absolute;inset:0;width:100%;height:100%;border:0"></iframe>
        </div>
    @elseif($getRecord()?->video_url)
        <a href="{{ $getRecord()->video_url }}" target="_blank" rel="noopener"
           class="fi-link text-primary-600 underline">{{ $getRecord()->video_url }}</a>
    @else
        <span class="text-gray-400">—</span>
    @endif
</div>
