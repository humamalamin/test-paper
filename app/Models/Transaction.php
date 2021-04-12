<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Searchable;

    protected $fillable = [
        'transaction_date',
        'reference',
        'amount',
        'account_id'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function searchableAs()
    {
        return 'transactions';
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'transaction_date' => $this->transaction_date,
            'amount' => $this->amount,
            'account_name' => $this->account->name
        ];
    }
}
