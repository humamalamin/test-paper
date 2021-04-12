<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="Account", title="Account")
 */
class AccountResource extends JsonResource
{
    /**
     * @OA\Property(
     *     property="id",
     *     type="integer"
     * ),
     * @OA\Property(
     *     property="name",
     *     type="string"
     * ),
     * @OA\Property(
     *     property="description",
     *     type="string"
     * ),
     * @OA\Property(
     *     property="account_type",
     *     type="string"
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
            'name' => $this->name,
            'description' => $this->description,
            'account_type' => $this->account_type,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d\TH:i:s\Z'): '',
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d\TH:i:s\Z'): '',
        ];
    }
}
