<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandeCompte extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'status', 'edited_by'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function editedBy()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}
