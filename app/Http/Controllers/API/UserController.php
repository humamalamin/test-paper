<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *   path="/api/admin/users/{userID}",
     *     tags={"user"},
     *     summary="Get user by ID",
     *     description="Returns a single user data",
     *     operationId="getUserByID",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="An authorization header is required after login",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="userID",
     *         in="path",
     *         description="ID of user to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                          allOf={
     *                              @OA\Schema(ref="#/components/schemas/User")
     *                          }
     *                      )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data not found"
     *     ),
     * )
     *
     * @param  int  $userID
     * @return Illuminate\Http\Resources\Json\Resource
     */
    public function show($userID)
    {
        $user = $this->repository->getByID($userID);

        return new UserResource($user);
    }
}
