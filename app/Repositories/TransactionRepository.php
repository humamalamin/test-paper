<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class TransactionRepository
{
    protected $model;

    public function __construct(Transaction $model)
    {
        $this->model = $model;
    }

    public function getAll(Request $request)
    {
        return QueryBuilder::for($this->model, $request)
            ->allowedIncludes('account')
            ->allowedFilters(['transaction_date', 'reference', 'amount', 'account.name'])
            ->orderBy('transaction_date', 'desc')
            ->paginate($request->page ? $request->page : 10);
    }

    public function getByID(Request $request, $transactionID)
    {
        return QueryBuilder::for($this->model, $request)
            ->allowedIncludes('account')
            ->findOrFail($transactionID);
    }

    public function store(Request $request)
    {
        $data = $this->setDataPayload($request);
        $item = $this->model;
        $item->fill($data);
        $item->save();

        return $item;
    }

    public function update($accountID, Request $request)
    {
        $data = $this->setDataPayload($request);
        $item = $this->model->findOrFail($accountID);
        $item->fill($data);
        $item->save();

        return $item;
    }

    public function delete($accountID)
    {
        return $this->model->destroy($accountID);
    }

    public function get()
    {
        return $this->model->select(DB::raw('SUM(amount) as total_amount'), DB::raw('MONTH(transaction_date) as month'))
            ->groupBy(DB::raw('MONTH(transaction_date)'))
            ->get();
    }

    public function getDaily()
    {
        return $this->model->whereDay('transaction_date', date('d'))->sum('amount');
    }

    protected function setDataPayload(Request $request)
    {
        return $request->all();
    }
}