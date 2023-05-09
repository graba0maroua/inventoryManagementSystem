<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipeLocalite extends Model
{
    use HasFactory;
    protected $table = 'dbo.equipe_localite';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'GROUPE_ID';

    protected $fillable = [
        'GROUPE_ID',
        'LOC_ID',
        'COP_ID',
    ];
}
