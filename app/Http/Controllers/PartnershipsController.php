<?php

namespace App\Http\Controllers;

use App\Models\PartnerInstitution;

class PartnershipsController extends Controller
{
    public function index()
    {
        $partners = PartnerInstitution::featured()
            ->orderBy('sort_order')
            ->get();

        return view('pages.partnerships', compact('partners'));
    }
}
