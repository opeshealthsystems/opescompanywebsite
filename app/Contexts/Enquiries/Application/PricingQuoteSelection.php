<?php

namespace App\Contexts\Enquiries\Application;

/** Server-owned descriptions for the public pricing page quote choices. */
final class PricingQuoteSelection
{
    private const OPTIONS = [
        'clinic-saas' => ['opes-clinic', 'OPES Clinic', 'SaaS', 'From 150,000 FCFA per month', 'À partir de 150 000 FCFA par mois'],
        'clinic-perpetual' => ['opes-clinic', 'OPES Clinic', 'Perpetual licence', 'From 2,500,000 FCFA plus 15–20% annual maintenance', 'À partir de 2 500 000 FCFA, plus 15–20 % de maintenance annuelle'],
        'hospital-small' => ['opes-hospital', 'OPES Hospital', 'Small hospital perpetual licence', 'From 15,000,000 FCFA plus 15–20% annual maintenance', 'À partir de 15 000 000 FCFA, plus 15–20 % de maintenance annuelle'],
        'hospital-medium-large' => ['opes-hospital', 'OPES Hospital', 'Medium or large hospital perpetual licence', 'From 25,000,000–50,000,000 FCFA plus 15–20% annual maintenance', 'À partir de 25 000 000–50 000 000 FCFA, plus 15–20 % de maintenance annuelle'],
        'specialty-lab' => ['opes-lab-is', 'OPES Lab IS', 'Perpetual licence', 'From 3,000,000 FCFA plus 15–20% annual maintenance', 'À partir de 3 000 000 FCFA, plus 15–20 % de maintenance annuelle'],
        'specialty-pharmacy' => ['opes-pharmacy-is', 'OPES Pharmacy IS', 'Perpetual licence', 'From 2,500,000 FCFA plus 15–20% annual maintenance', 'À partir de 2 500 000 FCFA, plus 15–20 % de maintenance annuelle'],
        'specialty-radiology' => ['opes-radiology-is', 'OPES Radiology IS', 'Perpetual licence', 'From 4,000,000 FCFA plus 15–20% annual maintenance', 'À partir de 4 000 000 FCFA, plus 15–20 % de maintenance annuelle'],
        'specialty-cardiology' => ['opes-cardiology-is', 'OPES Cardiology IS', 'Perpetual licence', 'From 4,000,000 FCFA plus 15–20% annual maintenance', 'À partir de 4 000 000 FCFA, plus 15–20 % de maintenance annuelle'],
        'specialty-dental' => ['opes-dental-is', 'OPES Dental IS', 'Perpetual licence', 'From 2,500,000 FCFA plus 15–20% annual maintenance', 'À partir de 2 500 000 FCFA, plus 15–20 % de maintenance annuelle'],
        'specialty-dermatology' => ['opes-dermatology-is', 'OPES Dermatology IS', 'Perpetual licence', 'From 2,500,000 FCFA plus 15–20% annual maintenance', 'À partir de 2 500 000 FCFA, plus 15–20 % de maintenance annuelle'],
        'specialty-pediatrics' => ['opes-pediatrics-is', 'OPES Pediatrics IS', 'Perpetual licence', 'From 3,000,000 FCFA plus 15–20% annual maintenance', 'À partir de 3 000 000 FCFA, plus 15–20 % de maintenance annuelle'],
        'specialty-obgyn' => ['opes-obstetrics-gynecology-is', 'OPES Obstetrics & Gynecology IS', 'Perpetual licence', 'From 3,500,000 FCFA plus 15–20% annual maintenance', 'À partir de 3 500 000 FCFA, plus 15–20 % de maintenance annuelle'],
        'care-single-hospital' => ['opes-care', 'OPES Care', 'Single hospital deployment', 'From 25,000,000 FCFA', 'À partir de 25 000 000 FCFA'],
        'care-multi-hospital' => ['opes-care', 'OPES Care', 'Multi-hospital network', 'From 50,000,000 FCFA', 'À partir de 50 000 000 FCFA'],
        'care-national' => ['opes-care', 'OPES Care', 'National or regional programme', 'Custom quotation requested', 'Devis personnalisé demandé'],
        'health-id-facility' => ['opes-health-id', 'OPES Health ID', 'Facility deployment', 'From 5,000,000 FCFA', 'À partir de 5 000 000 FCFA'],
        'health-id-multi-facility' => ['opes-health-id', 'OPES Health ID', 'Multi-facility deployment', 'Custom quotation requested', 'Devis personnalisé demandé'],
        'health-id-national' => ['opes-health-id', 'OPES Health ID', 'National deployment', 'Custom quotation requested', 'Devis personnalisé demandé'],
        'clinical-cdms-saas' => ['opes-cdms', 'Clinical Document Management System', 'SaaS', '100,000 FCFA per month', '100 000 FCFA par mois'],
        'clinical-cdms-perpetual' => ['opes-cdms', 'Clinical Document Management System', 'Perpetual licence', '4,000,000 FCFA', '4 000 000 FCFA'],
        'clinical-triage-saas' => ['opes-triage', 'OPES Triage', 'SaaS', '150,000 FCFA per month', '150 000 FCFA par mois'],
        'clinical-triage-perpetual' => ['opes-triage', 'OPES Triage', 'Perpetual licence', '5,000,000 FCFA', '5 000 000 FCFA'],
        'clinical-cdss-hospital' => ['opes-cdss', 'OPES Clinical Decision Support', 'Hospital deployment', '10,000,000 FCFA', '10 000 000 FCFA'],
        'clinical-cdss-network' => ['opes-cdss', 'OPES Clinical Decision Support', 'Network or national deployment', 'Custom quotation requested', 'Devis personnalisé demandé'],
        'government-insurance' => ['opes-insurance', 'OPES Insurance', 'Custom deployment', 'Custom quotation requested', 'Devis personnalisé demandé'],
        'government-public-health' => ['opes-public-health', 'OPES Public Health', 'Custom deployment', 'Custom quotation requested', 'Devis personnalisé demandé'],
        'government-national-platform' => ['national-digital-health-platform', 'National Digital Health Platform', 'Custom deployment', 'Custom quotation requested', 'Devis personnalisé demandé'],
        'service-implementation' => ['implementation', 'Implementation', 'Professional service', 'From 1,500,000 FCFA', 'À partir de 1 500 000 FCFA'],
        'service-data-migration' => ['data-migration', 'Data migration', 'Professional service', 'From 1,000,000 FCFA', 'À partir de 1 000 000 FCFA'],
        'service-training' => ['training', 'Training', 'Professional service', 'From 500,000 FCFA', 'À partir de 500 000 FCFA'],
        'service-custom-integration' => ['custom-integration', 'Custom integration', 'Professional service', 'Custom quotation requested', 'Devis personnalisé demandé'],
        'maintenance-standard' => ['maintenance-support', 'Maintenance & support', 'Standard annual plan', '15% of licence value per year', '15 % de la valeur de la licence par an'],
        'maintenance-premium' => ['maintenance-support', 'Maintenance & support', 'Premium annual plan', '20% of licence value per year', '20 % de la valeur de la licence par an'],
        'maintenance-enterprise' => ['maintenance-support', 'Maintenance & support', 'Enterprise service contract', 'Custom quotation requested', 'Devis personnalisé demandé'],
        'managed-hosting' => ['managed-hosting', 'Managed hosting services', 'Monthly hosting', 'From 100,000 FCFA per month', 'À partir de 100 000 FCFA par mois'],
        'custom-proposal' => ['custom-proposal', 'Custom OPES proposal', 'Organisation-specific deployment', 'Custom quotation requested', 'Devis personnalisé demandé'],
    ];

    public static function keys(): array
    {
        return array_keys(self::OPTIONS);
    }

    public static function find(?string $key, string $locale = 'en'): ?array
    {
        if ($key === null || !isset(self::OPTIONS[$key])) return null;

        [$slug, $product, $option, $priceEn, $priceFr] = self::OPTIONS[$key];

        return [
            'key' => $key,
            'product_slug' => $slug,
            'product' => $product,
            'option' => $option,
            'price' => $locale === 'fr' ? $priceFr : $priceEn,
            'locale' => $locale === 'fr' ? 'fr' : 'en',
        ];
    }
}
