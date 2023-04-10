<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiensScannes extends Model
{
    use HasFactory;
    protected $table = 'INV.T_BIENS_SCANNES';
    protected $primaryKey = 'INV_ID';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    public function localite(): BelongsTo
    {
        return $this->belongsTo(Localite::class, 'LOC_ID');
    }
}
