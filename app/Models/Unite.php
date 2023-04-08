<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unite extends Model
{
    use HasFactory;

    protected $table = 'INV.T_R_UNITE_COMPTABLE_UCM';
    protected $primaryKey = 'UCM_ID';
    public $timestamps = false;

    public function Centres(): HasMany
    {
        return $this->hasMany(Centre::class);
    }

}
