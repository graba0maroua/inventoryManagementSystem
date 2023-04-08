<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localite extends Model
{
    use HasFactory;

    protected $table = 'INV.T_E_LOCATION_LOC';
    protected $primaryKey = 'LOC_ID';
    public $timestamps = false;
}
