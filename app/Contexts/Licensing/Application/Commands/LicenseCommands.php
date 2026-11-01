<?php

namespace App\Contexts\Licensing\Application\Commands;

use App\Contexts\Shared\Application\{WorkflowInput, WorkflowResult};
use App\Contexts\Shared\Infrastructure\DomainMail;
use App\Models\{License, User};
use App\Support\ProductCatalog;
use Illuminate\Validation\Rule;

final class LicenseCommands
{
    public function create(WorkflowInput $input): WorkflowResult
    {
        $data = $this->values($input);
        $license = License::create([...$data, 'issued_by' => $input->user()->id, 'license_key' => $data['license_key'] ?? License::generateKey()]);
        $customer = $license->customer;
        DomainMail::to($customer->email)->queue(new \App\Mail\LicenseIssued($license));
        $customer->notify(new \App\Notifications\FeedEntry('licensing.issued', 'License issued', 'A license for '.$license->product_name.' was issued to your account.',
            'key', route('customer.licenses.show', ['locale' => 'en', 'id' => $license->id])));
        return WorkflowResult::payload(['record' => $license], 201);
    }

    public function update(WorkflowInput $input, License $license): WorkflowResult
    {
        $license->update($this->values($input, $license));
        return WorkflowResult::payload(['record' => $license]);
    }

    public function suspend(WorkflowInput $input, License $license): WorkflowResult
    {
        abort_unless(in_array($license->status, ['active', 'suspended'], true), 409);
        $license->update(['status' => 'suspended']);
        return WorkflowResult::payload(['record' => $license]);
    }

    public function reactivate(WorkflowInput $input, License $license): WorkflowResult
    {
        abort_unless(in_array($license->status, ['active', 'suspended'], true) && !$license->isExpired(), 409);
        $license->update(['status' => 'active']);
        return WorkflowResult::payload(['record' => $license]);
    }

    public function delete(WorkflowInput $input, License $license): WorkflowResult
    {
        $id = $license->id; $license->delete();
        return WorkflowResult::payload(['deleted_id' => $id]);
    }

    private function values(WorkflowInput $input, ?License $license = null): array
    {
        $data = $input->validate(['user_id' => 'required|exists:users,id', 'product_slug' => ['required', Rule::in(array_keys(ProductCatalog::options()))],
            'license_key' => ['nullable', 'string', 'max:50', Rule::unique('licenses', 'license_key')->ignore($license?->id)],
            'plan' => 'required|in:starter,standard,professional,enterprise', 'seats' => 'required|integer|min:1|max:9999',
            'status' => 'required|in:active,suspended,expired,cancelled', 'start_date' => 'required|date', 'end_date' => 'required|date|after:start_date',
            'price' => 'nullable|integer|min:0', 'currency' => 'required|in:XAF,USD,EUR', 'notes' => 'nullable|string|max:20000']);
        abort_unless(User::whereKey($data['user_id'])->whereHas('roles', fn ($q) => $q->where('name', 'customer'))->exists(), 422);
        if ($data['status'] === 'active') abort_if(\Carbon\Carbon::parse($data['end_date'])->endOfDay()->isPast(), 422);
        $data['product_name'] = ProductCatalog::options()[$data['product_slug']];
        if ($license && empty($data['license_key'])) unset($data['license_key']);
        return $data;
    }
}
