<?php

namespace App\Models;

use App\Models\Localite;
use App\Models\Unite;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assets extends Model
{
    use HasFactory;
    protected $table = 'INV.T_E_ASSET_AST';
    protected $primaryKey = 'AST';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    public function bienScannes(): HasMany
    {
        return $this->hasMany(BienScanne::class, 'code_bar', 'AST_CB');
    }
}
