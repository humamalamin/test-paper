<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;

class AuthRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }
    
    public function store(Request $request)
    {
        $data = $this->setDataPayload($request);
        $item = $this->model;
        $item->email = $data['email'];
        $item->name = $data['name'];
        $item->number_verified = $data['number_verified'];
        $item->password = bcrypt($data['password']);
        $item->save();

        return $item;
    }

    protected function setDataPayload(Request $request)
    {
        return $request->all();
    }
}