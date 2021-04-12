<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use App\Repositories\AccountRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    protected $repository;

    public function __construct(AccountRepository $repository)
    {
        $this->repository = $repository;        
    }

    /**
     * @OA\Get(
     *   path="/api/admin/accounts",
     *     tags={"account"},
     *     summary="Fetch all accounts",
     *     description="Returns all accounts",
     *     operationId="listAccounts",
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
     *         name="search",
     *         in="query",
     *         description="for search data",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of Accounts",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                          allOf={
     *                              @OA\Schema(ref="#/components/schemas/Account")
     *                          }
     *                      )
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * @return Illuminate\Http\Resources\Json\Resource
     */
    public function index(Request $request)
    {
        $accounts = $this->repository->getAll($request->search ? $request->search : null,
            $request->page ? $request->page : null, $request->orderBy, $request->orderType);

        return AccountResource::collection($accounts);
    }

    /**
     * @OA\Get(
     *   path="/api/admin/accounts/{accountID}",
     *     tags={"account"},
     *     summary="Get account by ID",
     *     description="Returns a single account data",
     *     operationId="getAccountByID",
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
     *         name="accountID",
     *         in="path",
     *         description="ID of account to return",
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
     *                              @OA\Schema(ref="#/components/schemas/Account")
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
     * @param  int  $accountID
     * @return Illuminate\Http\Resources\Json\Resource
     */
    public function show($accountID)
    {
        $account = $this->repository->getByID($accountID);

        if (empty($account)) {
            return response()->json([
                'data' => null,
                'status' => 404,
                'message' => 'Data not found!',
                'errors' => null
            ], 404);
        }

        return new AccountResource($account);
    }

    /**
     * @OA\Post(
     *   path="/api/admin/accounts",
     *     tags={"account"},
     *     summary="Add a new Accounts",
     *     description="Adding new account data",
     *     operationId="addAccount",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="An authorization header is required after login",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="account_type",
     *                     type="string",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Missing account parameter"
     *     )
     * )
     *
     * @param  Request  $request
     * @return Illuminate\Http\Resources\Json\Resource
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'required',
            'account_type' => 'required'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'data' => null,
                'status' => 422,
                'message' => $validated->messages()->first(),
                'errors' => null
            ], 422);
        }

        $account = $this->repository->store($request);

        return new AccountResource($account);
    }

    /**
     * @OA\Put(
     *   path="/api/admin/accounts/{accountID}",
     *     tags={"account"},
     *     summary="Edit account",
     *     description="Updating account data",
     *     operationId="editAccount",
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
     *         name="accountID",
     *         in="path",
     *         description="ID of account post to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="account_type",
     *                     type="string",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Missing account parameter"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Account not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid ID account"
     *     )
     * )
     *
     * @param  Request  $request
     * @param  int  $accountID
     * @return Illuminate\Http\Resources\Json\Resource
     */
    public function update(Request $request, $accountID)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'required',
            'account_type' => 'required'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'data' => null,
                'status' => 422,
                'message' => $validated->messages()->first(),
                'errors' => null
            ], 422);
        }

        $account = $this->repository->update($accountID, $request);

        return new AccountResource($account);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/accounts/{accountID}",
     *     tags={"account"},
     *     summary="Delete account based on ID",
     *     description="",
     *     operationId="deleteAccountById",
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
     *         name="accountID",
     *         in="path",
     *         description="ID of account to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Account not found"
     *     )
     * )
     *
     * @param $accountID
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function delete($accountID)
    {
        $this->repository->delete($accountID);

        return response()->json(['status' => 200, 'data' => null, 'errors' => [], 'message' => null], 200);
    }
}
