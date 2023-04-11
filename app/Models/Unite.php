<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Unite extends Model
{
    use HasFactory;

    protected $table = 'INV.T_R_UNITE_COMPTABLE_UCM';
    protected $primaryKey = 'UCM_ID';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    public function Centres(): HasMany
    {
        return $this->hasMany(Centre::class,'UCM_ID','UCM_ID');
    }
    public function CentreLocalite(): HasManyThrough
    {
        return $this->hasManyThrough(
            Localite::class,
            Centre::class,
            'UCM_ID', // Foreign key on the centre table
            'COP_ID', // Foreign key on the localite table
            'UCM_ID', // Local key on the unite table
            'COP_ID' // Local key on the centre table
        );
    }
    public function users()
    {
        return $this->morphOne(User::class, 'structure');
    }
}
