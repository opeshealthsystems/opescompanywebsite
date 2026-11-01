<!doctype html>
<html lang="{{ $french ? 'fr' : 'en' }}">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;background:#f3f6fa;font-family:Arial,sans-serif;color:#183853;line-height:1.6">
<main style="max-width:640px;margin:28px auto;padding:28px;background:#fff;border:1px solid #d9e2ec;border-radius:8px">
    <p style="margin:0 0 8px;color:#2369a6;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase">OPES Health Systems</p>
    <h1 style="margin:0 0 16px;color:#082d55;font-size:24px">{{ $french ? 'Copie de votre demande de devis' : 'Copy of your quote request' }}</h1>
    <p>{{ $french ? 'Bonjour' : 'Hello' }} {{ $lead->name }},</p>
    <p>{{ $french ? 'Nous avons reçu votre demande. Voici une copie de l’option et du prix indicatif que vous avez sélectionnés :' : 'We received your request. Here is a copy of the option and indicative price you selected:' }}</p>
    <table role="presentation" style="width:100%;border-collapse:collapse;margin:20px 0">
        <tr><th align="left" style="padding:10px;border:1px solid #d9e2ec;background:#f5f8fc">{{ $french ? 'Produit' : 'Product' }}</th><td style="padding:10px;border:1px solid #d9e2ec">{{ $product }}</td></tr>
        <tr><th align="left" style="padding:10px;border:1px solid #d9e2ec;background:#f5f8fc">{{ $french ? 'Option' : 'Option' }}</th><td style="padding:10px;border:1px solid #d9e2ec">{{ $option }}</td></tr>
        <tr><th align="left" style="padding:10px;border:1px solid #d9e2ec;background:#f5f8fc">{{ $french ? 'Prix affiché sélectionné' : 'Selected displayed price' }}</th><td style="padding:10px;border:1px solid #d9e2ec">{{ $price }}</td></tr>
        <tr><th align="left" style="padding:10px;border:1px solid #d9e2ec;background:#f5f8fc">{{ $french ? 'Référence' : 'Reference' }}</th><td style="padding:10px;border:1px solid #d9e2ec">{{ $reference }}</td></tr>
    </table>
    @if($lead->message)
        <h2 style="font-size:16px;color:#082d55">{{ $french ? 'Votre message' : 'Your message' }}</h2>
        <p style="white-space:pre-wrap">{{ $lead->message }}</p>
    @endif
    <p style="padding:14px;background:#f5f8fc;border-left:3px solid #7f9eb8;color:#40586e;font-size:13px">
        {{ $french ? 'Ce récapitulatif confirme votre demande et le prix indicatif affiché. Il ne constitue pas une offre commerciale ni un devis définitif. Le prix, le périmètre, la disponibilité et les conditions seront confirmés dans un devis formel.' : 'This summary confirms your request and the indicative price shown. It is not a commercial offer or a final quotation. Price, scope, availability and terms will be confirmed in a formal quote.' }}
    </p>
    <p>{{ $french ? 'Notre équipe commerciale examinera votre demande et vous recontactera.' : 'Our commercial team will review your request and follow up with you.' }}</p>
    <p style="margin-bottom:0">OPES Health Systems · <a href="mailto:{{ config('website_forms.recipient') }}" style="color:#145c95">{{ config('website_forms.recipient') }}</a></p>
</main>
</body>
</html>
