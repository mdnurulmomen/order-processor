<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class GeneralInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            'delivery_charge' => $this->delivery_charge,
            'multiple_delivery_charge_percentage' => $this->multiple_delivery_charge_percentage,
            'official_mail_address' => $this->official_mail_address,
            'official_contact_address' => $this->official_contact_address,
            'official_customer_care_number' => $this->official_customer_care_number,
            'vat_rate' => $this->vat_rate,
            'official_bank' => $this->official_bank,
            'official_bank_account_holder_name' => $this->official_bank_account_holder_name,
            'official_bank_account_number' => $this->official_bank_account_number,
            'merchant_number' => $this->merchant_number,
            'logo' => $this->logo,
            'favicon' => $this->favicon,
        ];
    }
}
