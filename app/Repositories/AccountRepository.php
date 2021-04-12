<?php

namespace App\Repositories;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountRepository
{
    protected $model;

    public function __construct(Account $model)
    {
        $this->model = $model;
    }

    public function getAll($search = null, $page = 10, $orderBy = "name", $orderType = "asc")
    {
        return $search == null ? $this->model->orderBy($orderBy, $orderType)->paginate($page) :
            $this->model->search($search)->orderBy($orderBy, $orderType)->paginate($page);
    }

    public function getByID($accountID)
    {
        return $this->model->find($accountID);
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

    protected function setDataPayload(Request $request)
    {
        return $request->all();
    }
}