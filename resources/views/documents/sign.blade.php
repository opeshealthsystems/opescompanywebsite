<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign Document — OPES Health Systems</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body" style="align-items:flex-start; padding-top:2rem;">
    <div style="width:100%;max-width:720px;margin:0 auto;padding:0 1rem 4rem;">
        <div style="text-align:center;margin-bottom:2rem;">
            <span class="auth-brand-opes">OPES</span>
            <span class="auth-brand-name"> Health Systems</span>
        </div>

        @if($expired)
            <div class="auth-card" style="text-align:center;padding:3rem;">
                <div style="font-size:3rem;margin-bottom:1rem;">&#9888;</div>
                <h1 style="color:#f1f5f9;font-size:1.25rem;font-weight:700;">Signing Link Expired</h1>
                <p style="color:#64748b;margin-top:0.5rem;">This document signing link has expired or has already been used.</p>
                <p style="color:#64748b;font-size:0.8125rem;margin-top:1rem;">If you need to sign this document, please contact <a href="mailto:support@opeshealthsystems.com" class="auth-link">support@opeshealthsystems.com</a>.</p>
            </div>
        @elseif($document->isSigned())
            <div class="auth-card" style="text-align:center;padding:3rem;">
                <div style="font-size:3rem;margin-bottom:1rem;color:#00C896;">&#10003;</div>
                <h1 style="color:#f1f5f9;font-size:1.25rem;font-weight:700;">Document Already Signed</h1>
                <p style="color:#64748b;margin-top:0.5rem;">Signed by <strong style="color:#e2e8f0;">{{ $document->signed_by_name }}</strong> on {{ $document->signed_at?->format('d M Y') }}.</p>
            </div>
        @else
            <div class="auth-card">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
                    <div>
                        <h1 class="auth-heading">{{ $document->title }}</h1>
                        <p class="auth-subheading">Ref: {{ $document->reference_number }} &middot; Addressee: {{ $document->addressee_name }}</p>
                    </div>
                    <span style="background:rgba(234,179,8,0.15);color:#eab308;font-size:0.75rem;font-weight:600;padding:0.3rem 0.75rem;border-radius:20px;text-transform:uppercase;letter-spacing:0.05em;">
                        Awaiting Signature
                    </span>
                </div>

                <div style="background:#0F172A;border:1px solid #334155;border-radius:8px;padding:1.5rem;max-height:400px;overflow-y:auto;margin-bottom:1.5rem;">
                    {!! $document->body_rendered !!}
                </div>

                @if ($errors->any())
                    <div class="auth-error-box" style="margin-bottom:1rem;">
                        @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('documents.sign.submit', $document->signature_token) }}" id="sign-form">
                    @csrf

                    <div class="auth-field" style="margin-bottom:1rem;">
                        <label for="typed_name" class="auth-label">Type your full name to sign *</label>
                        <input id="typed_name" name="typed_name" type="text"
                            class="auth-input @error('typed_name') auth-input-error @enderror"
                            value="{{ old('typed_name', $document->addressee_name) }}"
                            required placeholder="Type your full legal name"
                            autocomplete="name">
                    </div>

                    <div class="auth-field" style="margin-bottom:1.5rem;">
                        <label class="auth-label">Optional: Draw your signature</label>
                        <div style="position:relative;">
                            <canvas id="sig-canvas" width="640" height="140"
                                style="border:1px solid #334155;border-radius:8px;background:#fff;width:100%;cursor:crosshair;touch-action:none;">
                            </canvas>
                            <button type="button" id="clear-canvas"
                                style="position:absolute;top:0.5rem;right:0.5rem;background:rgba(15,23,42,0.8);color:#94a3b8;border:1px solid #334155;border-radius:4px;padding:0.25rem 0.5rem;font-size:0.75rem;cursor:pointer;">
                                Clear
                            </button>
                        </div>
                        <input type="hidden" name="canvas_data" id="canvas_data">
                        <p style="color:#475569;font-size:0.75rem;margin-top:0.375rem;">Drawing is optional — your typed name above is the binding signature.</p>
                    </div>

                    <div style="background:rgba(0,200,150,0.05);border:1px solid rgba(0,200,150,0.15);border-radius:8px;padding:1rem;margin-bottom:1.5rem;">
                        <p style="color:#94a3b8;font-size:0.8125rem;line-height:1.6;">
                            By clicking "Sign Document" below, you agree that your typed name constitutes a legal digital signature on this document, and that you have read and understood its contents. Your signature will be timestamped and your IP address recorded.
                        </p>
                    </div>

                    <button type="submit" class="auth-btn" id="sign-btn">Sign Document</button>
                </form>
            </div>
        @endif

        <p class="auth-footer-note" style="margin-top:1.5rem;text-align:center;">
            &copy; {{ date('Y') }} OPES Health Systems SARL &mdash; Douala, Cameroon
        </p>
    </div>

    <script>
    (function() {
        var canvas = document.getElementById('sig-canvas');
        if (!canvas) return;
        var ctx = canvas.getContext('2d');
        var drawing = false, hasDrawing = false;

        function getPos(e) {
            var rect = canvas.getBoundingClientRect();
            var scaleX = canvas.width / rect.width;
            var scaleY = canvas.height / rect.height;
            var src = e.touches ? e.touches[0] : e;
            return {
                x: (src.clientX - rect.left) * scaleX,
                y: (src.clientY - rect.top) * scaleY
            };
        }

        ctx.strokeStyle = '#0f172a';
        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        canvas.addEventListener('mousedown', function(e) { drawing = true; var p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
        canvas.addEventListener('mousemove', function(e) { if (!drawing) return; hasDrawing = true; var p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
        canvas.addEventListener('mouseup', function() { drawing = false; });
        canvas.addEventListener('mouseleave', function() { drawing = false; });
        canvas.addEventListener('touchstart', function(e) { e.preventDefault(); drawing = true; var p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
        canvas.addEventListener('touchmove', function(e) { e.preventDefault(); if (!drawing) return; hasDrawing = true; var p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
        canvas.addEventListener('touchend', function() { drawing = false; });

        document.getElementById('clear-canvas').addEventListener('click', function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasDrawing = false;
            document.getElementById('canvas_data').value = '';
        });

        document.getElementById('sign-form').addEventListener('submit', function() {
            if (hasDrawing) {
                document.getElementById('canvas_data').value = canvas.toDataURL('image/png');
            }
        });
    })();
    </script>
</body>
</html>
