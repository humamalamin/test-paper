<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="User", title="User")
 */
class UserResource extends JsonResource
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
     *     property="email",
     *     type="string"
     * ),
     * @OA\Property(
     *     property="number_verified",
     *     type="integer"
     * ),
     * @OA\Property(
     *     property="is_active",
     *     type="boolean"
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
            'email' => $this->email,
            'number_verified' => $this->number_verified,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d\TH:i:s\Z'): '',
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d\TH:i:s\Z'): '',
        ];
    }
}
