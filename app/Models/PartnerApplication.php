<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(\App\Observers\PartnerApplicationObserver::class)]
class PartnerApplication extends Model
{
    protected $fillable = [
        'organization_name', 'contact_name', 'email', 'phone',
        'country', 'city', 'partner_type', 'organization_type',
        'annual_revenue_range', 'target_market', 'description',
        'website', 'locale', 'status', 'admin_notes', 'ip_address',
    ];

    public static array $partnerTypes = [
        'reseller'       => ['en' => 'Reseller / Distributor', 'fr' => 'Revendeur / Distributeur'],
        'technology'     => ['en' => 'Technology Partner', 'fr' => 'Partenaire technologique'],
        'implementation' => ['en' => 'Implementation Partner', 'fr' => 'Partenaire d\'implémentation'],
        'referral'       => ['en' => 'Referral Partner', 'fr' => 'Partenaire référent'],
        'hospital'       => ['en' => 'Hospital / Clinical Partner', 'fr' => 'Partenaire hospitalier / clinique'],
        'ngo'            => ['en' => 'NGO / Development Partner', 'fr' => 'ONG / Partenaire de développement'],
        'investor'       => ['en' => 'Investor', 'fr' => 'Investisseur'],
    ];

    public static array $statuses = ['pending', 'reviewing', 'approved', 'rejected'];
}
