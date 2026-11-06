<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(\App\Observers\PartnerApplicationObserver::class)]
class PartnerApplication extends Model
{
    protected $fillable = [
        'organization_name', 'contact_name', 'email', 'phone',
        'country', 'city', 'partner_type', 'organization_type',
        'annual_revenue_range', 'target_market', 'description',
        'website', 'locale', 'status', 'admin_notes', 'ip_address',
    ];

    public function memberships(): HasMany
    {
        return $this->hasMany(PartnerApplicationMembership::class);
    }

    public static array $partnerTypes = [
        'reseller'       => ['en' => 'Reseller / Distributor', 'fr' => 'Revendeur / Distributeur'],
        'technology'     => ['en' => 'Technology Partner', 'fr' => 'Partenaire technologique'],
        'implementation' => ['en' => 'Implementation Partner', 'fr' => 'Partenaire d\'implémentation'],
        'referral'       => ['en' => 'Referral Partner', 'fr' => 'Partenaire référent'],
        'hospital'       => ['en' => 'Hospital / Clinical Partner', 'fr' => 'Partenaire hospitalier / clinique'],
        'academic'       => ['en' => 'Academic / Research Institution', 'fr' => 'Institution académique / de recherche'],
        'ngo'            => ['en' => 'NGO / Development Partner', 'fr' => 'ONG / Partenaire de développement'],
        'health_ai'      => ['en' => 'Healthcare AI Company', 'fr' => 'Entreprise d’IA en santé'],
        'investor'       => ['en' => 'Investor', 'fr' => 'Investisseur'],
    ];

    public static array $statuses = ['pending', 'reviewing', 'approved', 'rejected'];
}
