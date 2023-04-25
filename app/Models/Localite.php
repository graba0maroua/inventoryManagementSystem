<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Thiagoprz\CompositeKey\HasCompositeKey;

class Localite extends Model
{
    use HasFactory;
    use HasCompositeKey;

    protected $table = 'INV.T_E_LOCATION_LOC';
    protected $primaryKey = ['LOC_ID','COP_ID'];
    public $incrementing = false;
    public $timestamps = false;
    // protected $keyType = 'string';

    public function Centre(): BelongsTo
    {
        return $this->belongsTo(Centre::class, 'COP_ID');
    }
    public function biensScannes(): HasMany
    {
        return $this->hasMany(BiensScannes::class,'LOC_ID','LOC_ID');
    }
    public function equipes()
    {
        return $this->belongsToMany(Equipe::class,'GROUPE_ID');
    }
}
