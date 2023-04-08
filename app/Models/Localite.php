<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Localite extends Model
{
    use HasFactory;

    protected $table = 'INV.T_E_LOCATION_LOC';
    protected $primaryKey = 'LOC_ID';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    public function Centre(): BelongsTo
    {
        return $this->belongsTo(Centre::class, 'COP_ID');
    }
}
