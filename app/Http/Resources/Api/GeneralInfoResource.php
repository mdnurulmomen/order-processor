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
            'deliveryCharge' => $this->delivery_charge,
            'deliveryChargePercentage' => $this->multiple_delivery_charge_percentage,
            'mailAddress' => $this->official_mail_address,
            'contactAddress' => $this->official_contact_address,
            'customerCareNumber' => $this->official_customer_care_number,
            'vatPercentage' => $this->vat_rate,
            'officialBankName' => $this->official_bank,
            'accountHolderName' => $this->official_bank_account_holder_name,
            'accountNumber' => $this->official_bank_account_number,
            'officialMerchantNumber' => $this->merchant_number,
            'appLogo' => $this->logo,
            'faviconIcon' => $this->favicon,
        ];
    }
}
