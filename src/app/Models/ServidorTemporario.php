<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServidorTemporario extends Model
{
    use HasFactory;

    protected $table = 'servidor_temporario';
    protected $primaryKey = 'pes_id';
    public $timestamps = false;

    protected $fillable = [
        'st_data_admissao',
        'st_data_demissao'
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pes_id');
    }
}
