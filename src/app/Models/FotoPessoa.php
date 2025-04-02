<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoPessoa extends Model
{
    use HasFactory;

    protected $table = 'foto_pessoa';
    protected $primaryKey = 'fp_id';
    public $timestamps = false;

    protected $fillable = [
        'pes_id',
        'fp_data',
        'fp_bucket',
        'fp_hash'
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pes_id');
    }
}
