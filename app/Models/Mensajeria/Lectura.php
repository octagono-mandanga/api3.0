<?php

namespace App\Models\Mensajeria;

use Illuminate\Database\Eloquent\Model;

class Lectura extends Model
{
    protected $table = 'mensajeria.lecturas';

    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'mensaje_id',
        'participante_id',
        'leido_en',
    ];

    protected $casts = [
        'leido_en' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function mensaje()
    {
        return $this->belongsTo(Mensaje::class, 'mensaje_id');
    }

    public function participante()
    {
        return $this->belongsTo(Participante::class, 'participante_id');
    }
}
