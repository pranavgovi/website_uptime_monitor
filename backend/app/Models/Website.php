<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Website extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'url',
        'is_up',
        'last_checked_at',
    ];

    protected $casts = [
        'is_up' => 'boolean',
        'last_checked_at' => 'datetime',
    ]; //casts are used to convert the data type of the attribute (from "0" or "1) to boolean

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class); //belongTo relationship is returned
    }
}
