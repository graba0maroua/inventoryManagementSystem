<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    use HasFactory;
    protected $table = 'equipes';
    protected $fillable = ['GROUPE_ID', 'EMP_ID', 'EMP_IS_MANAGER','YEAR','EMP_FULLNAME','COP_ID'];
    protected $primaryKey = ['GROUPE_ID'];
    public $incrementing = false;
    public function localites()
    {
        return $this->belongsToMany(Localite::class,'COP_ID');
    }
}
