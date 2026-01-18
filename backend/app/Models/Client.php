<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'email',
    ]; //fillable is the attributes that are mass assignable by the user
//This pattern helps to prevent users from mass assigning attributes that are not allowed
    public function websites(): HasMany
    {
        return $this->hasMany(Website::class); //relationship object is returned
    }
}
