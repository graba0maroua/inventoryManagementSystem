<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Centre extends Model
{
    use HasFactory;
    protected $table = 'INV.T_R_CENTRE_OPERATIONNEL_COP';
    protected $primaryKey = 'COP_ID';
    public $timestamps = false;

    public function Unite(): BelongsTo
    {
        return $this->belongsTo(Unite::class);
    }
    public function Localites(): HasMany
    {
        return $this->hasMany(Localite::class);
    }


}