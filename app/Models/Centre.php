<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Centre extends Model
{
    use HasFactory;
    protected $table = 'INV.T_R_CENTRE_OPERATIONNEL_COP';
    protected $primaryKey = 'COP_ID';
    public $timestamps = false;
}
