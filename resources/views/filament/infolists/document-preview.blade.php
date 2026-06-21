<div style="border: 1px solid #334155; border-radius: 8px; overflow: hidden; background: white;">
    <div style="background: #1e293b; padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center;">
        <span style="color: #9fb0c9; font-size: 0.8125rem;">Document Preview</span>
        <a href="{{ route('documents.pdf', $getRecord()) }}"
           target="_blank"
           style="color: #00C896; font-size: 0.75rem; text-decoration: none;">
            Download PDF ↗
        </a>
    </div>
    <div style="padding: 1.5rem; overflow-x: auto; max-height: 600px; overflow-y: auto;">
        {!! $getRecord()->body_rendered ?? '<em style="color:#9fb0c9;">No preview available.</em>' !!}
    </div>
</div>
