<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    use HasFactory;
    protected $table = 'dbo.equipe_Temp';
    protected $primaryKey = 'GROUP_ID';
    public $timestamps = false;
    public $incrementing = false;
    // protected $keyType = 'string';
}
