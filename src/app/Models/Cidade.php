<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    use HasFactory;

    protected $table = 'cidade';
    protected $primaryKey = 'cid_id';
    public $timestamps = false;

    protected $fillable = [
        'cid_nome',
        'cid_uf'
    ];

    public function enderecos()
    {
        return $this->hasMany(Endereco::class, 'cid_id');
    }
}