<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="Transaction", title="Transaction")
 */
class TransactionResource extends JsonResource
{
    /**
     * @OA\Property(
     *     property="id",
     *     type="integer"
     * ),
     * @OA\Property(
     *     property="transaction_date",
     *     type="string"
     * ),
     * @OA\Property(
     *     property="reference",
     *     type="string"
     * ),
     * @OA\Property(
     *     property="amount",
     *     type="integer"
     * ),
     * @OA\Property(
     *     property="account_id",
     *     type="integer"
     * ),
     * @OA\Property(
     *     property="created_at",
     *     type="string"
     * ),
     * @OA\Property(
     *     property="updated_at",
     *     type="string"
     * )
     *
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     *
     * @SuppressWarnings("unused")
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'transaction_date' => $this->transaction_date,
            'reference' => $this->reference,
            'amount' => $this->amount,
            'account_id' => $this->account_id,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d\TH:i:s\Z'): '',
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d\TH:i:s\Z'): '',
            'account' => new AccountResource($this->whenLoaded('account'))
        ];
    }
}
