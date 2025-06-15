<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Валидация формы коллбэк от WATA
 *
 * @class PaymentCallbackRequest
 */
class PaymentCallbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'transactionType'     => 'required|string|in:CardCrypto,SBP,T-Pay',
            'transactionId'       => 'required|uuid',
            'transactionStatus'   => 'required|string|in:Paid,Declined',
            'errorCode'           => 'nullable|string',
            'errorDescription'    => 'nullable|string',
            'terminalName'        => 'required|string',
            'amount'              => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'currency'            => 'required|string|in:RUB,USD,EUR',
            'orderId'             => 'required|string',
            'orderDescription'    => 'required|string',
            'commission'          => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'paymentTime'         => 'required|date',
            'email'               => 'nullable|email',
        ];
    }
}
