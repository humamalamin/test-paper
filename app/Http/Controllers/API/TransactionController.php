<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    protected $repository;

    public function __construct(TransactionRepository $repository)
    {
        $this->repository = $repository;        
    }

    /**
     * @OA\Get(
     *   path="/api/admin/transactions",
     *     tags={"transaction"},
     *     summary="Fetch all transactions",
     *     description="Returns all transactions",
     *     operationId="listTransactions",
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
     *                              @OA\Schema(ref="#/components/schemas/Transaction"),
     *                              @OA\Schema(
     *                                  @OA\Property(property="account", type="array", @OA\Items(
     *                                      allOf={
     *                                          @OA\Schema(ref="#/components/schemas/Account"),
     *                                      }
     *                                  )),
     *                              )
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
        $transactions = $this->repository->getAll($request);

        return TransactionResource::collection($transactions);
    }

    /**
     * @OA\Get(
     *   path="/api/admin/transactions/{transactionID}",
     *     tags={"transaction"},
     *     summary="Get transaction by ID",
     *     description="Returns a single transaction data",
     *     operationId="getTransactionByID",
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
     *         name="transactionID",
     *         in="path",
     *         description="ID of transaction to return",
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
     *                              @OA\Schema(ref="#/components/schemas/Transaction"),
      *                              @OA\Schema(
     *                                  @OA\Property(property="account", type="array", @OA\Items(
     *                                      allOf={
     *                                          @OA\Schema(ref="#/components/schemas/Account"),
     *                                      }
     *                                  )),
     *                              )
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
     * @param  int  $transactionID
     * @return Illuminate\Http\Resources\Json\Resource
     */
    public function show(Request $request, $transactionID)
    {
        $transaction = $this->repository->getByID($request, $transactionID);

        if (empty($transaction)) {
            return response()->json([
                'data' => null,
                'status' => 404,
                'message' => 'Data not found!',
                'errors' => null
            ], 404);
        }

        return new TransactionResource($transaction);
    }

    /**
     * @OA\Post(
     *   path="/api/admin/transactions",
     *     tags={"transaction"},
     *     summary="Add a new transactions",
     *     description="Adding new transaction data",
     *     operationId="addTransaction",
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
     *                     property="transaction_date",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="reference",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="amount",
     *                     type="integer",
     *                 ),
     *                 @OA\Property(
     *                     property="account_id",
     *                     type="integer",
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
     *         description="Missing transaction parameter"
     *     )
     * )
     *
     * @param  Request  $request
     * @return Illuminate\Http\Resources\Json\Resource
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'transaction_date' => 'required|date',
            'reference' => 'required',
            'amount' => 'required|numeric',
            'account_id' => 'required|exists:accounts,id'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'data' => null,
                'status' => 422,
                'message' => $validated->messages()->first(),
                'errors' => null
            ], 422);
        }

        $transaction = $this->repository->store($request);

        return new TransactionResource($transaction);
    }

    /**
     * @OA\Put(
     *   path="/api/admin/transactions/{transactionID}",
     *     tags={"transaction"},
     *     summary="Edit transaction",
     *     description="Updating transaction data",
     *     operationId="editTransaction",
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
     *         name="transactionID",
     *         in="path",
     *         description="ID of transaction post to return",
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
     *                     property="transaction_date",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="reference",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="amount",
     *                     type="integer",
     *                 ),
     *                 @OA\Property(
     *                     property="account_id",
     *                     type="integer",
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
     *         description="Missing transaction parameter"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="transaction not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid ID transaction"
     *     )
     * )
     *
     * @param  Request  $request
     * @param  int  $transactionID
     * @return Illuminate\Http\Resources\Json\Resource
     */
    public function update(Request $request, $transactionID)
    {
        $validated = Validator::make($request->all(), [
            'transaction_date' => 'required|date',
            'reference' => 'required',
            'amount' => 'required|numeric',
            'account_id' => 'required|exists:accounts,id'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'data' => null,
                'status' => 422,
                'message' => $validated->messages()->first(),
                'errors' => null
            ], 422);
        }

        $transaction = $this->repository->update($transactionID, $request);

        return new TransactionResource($transaction);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/transactions/{transactionID}",
     *     tags={"transaction"},
     *     summary="Delete transaction based on ID",
     *     description="",
     *     operationId="deleteTransactionById",
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
     *         name="transactionID",
     *         in="path",
     *         description="ID of transaction to return",
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
     *         description="transaction not found"
     *     )
     * )
     *
     * @param $transactionID
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function delete($transactionID)
    {
        $this->repository->delete($transactionID);

        return response()->json(['status' => 200, 'data' => null, 'errors' => [], 'message' => null], 200);
    }

    /**
     * @OA\Get(
     *   path="/api/admin/transactions/month",
     *     tags={"transaction"},
     *     summary="Get transaction by month",
     *     description="Returns a single transaction data",
     *     operationId="getTransactionByMonth",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="An authorization header is required after login",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
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
     *                              @OA\Schema(ref="#/components/schemas/Transaction"),
      *                              @OA\Schema(
     *                                  @OA\Property(property="account", type="array", @OA\Items(
     *                                      allOf={
     *                                          @OA\Schema(ref="#/components/schemas/Account"),
     *                                      }
     *                                  )),
     *                              )
     *                          }
     *                      )
     *                 )
     *             )
     *         )
     *     ),
     * )
     *
     * @return Illuminate\Http\Resources\Json\Resource
     */
    public function month()
    {
        $transactions = $this->repository->get();

        return response()->json(['data' => $transactions], 200);
    }

    /**
     * @OA\Get(
     *   path="/api/admin/transactions/daily",
     *     tags={"transaction"},
     *     summary="Get transaction by daily",
     *     description="Returns a single transaction data",
     *     operationId="getTransactionByDaily",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="An authorization header is required after login",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
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
     *                              @OA\Schema(ref="#/components/schemas/Transaction"),
      *                              @OA\Schema(
     *                                  @OA\Property(property="account", type="array", @OA\Items(
     *                                      allOf={
     *                                          @OA\Schema(ref="#/components/schemas/Account"),
     *                                      }
     *                                  )),
     *                              )
     *                          }
     *                      )
     *                 )
     *             )
     *         )
     *     ),
     * )
     *
     * @return Illuminate\Http\Resources\Json\Resource
     */
    public function daily()
    {
        $transaction = $this->repository->getDaily();

        return response()->json(['data' => $transaction], 200);
    }
}
