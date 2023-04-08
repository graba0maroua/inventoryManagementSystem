<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unite extends Model
{
    use HasFactory;

    protected $table = 'INV.T_R_UNITE_COMPTABLE_UCM';
    protected $primaryKey = 'UCM_ID';
    public $timestamps = false;

}
