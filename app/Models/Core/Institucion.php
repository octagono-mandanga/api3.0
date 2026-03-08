<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    use HasUuids;

    protected $table = 'core.instituciones';

    protected $fillable = [
        'plan_id',
        'tema_id',
        'municipio_id',
        'nit',
        'codigo_dane',
        'tipo_institucion',
        'nombre_legal',
        'nombre_corto',
        'direccion',
        'telefono',
        'email_oficial',
        'sitio_web',
        'dominio',
        'logo_url',
        'portada_url',
        'rector_id',
        'colores_marca',
        'estado',
    ];

    protected $casts = [
        'plan_id' => 'integer',
        'tema_id' => 'integer',
        'municipio_id' => 'integer',
        'colores_marca' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function tema()
    {
        return $this->belongsTo(Tema::class, 'tema_id');
    }

    public function municipio()
    {
        return $this->belongsTo(\App\Models\Ref\Municipio::class, 'municipio_id');
    }

    public function rector()
    {
        return $this->belongsTo(\App\Models\Auth\Usuario::class, 'rector_id');
    }

    public function sedes()
    {
        return $this->hasMany(Sede::class, 'institucion_id');
    }

    public function lectivos()
    {
        return $this->hasMany(Lectivo::class, 'institucion_id');
    }

    public function rolesInstitucion()
    {
        return $this->hasMany(RolInstitucion::class, 'institucion_id');
    }

    public function perfiles()
    {
        return $this->hasMany(Perfil::class, 'institucion_id');
    }

    public function nivelesInstitucion()
    {
        return $this->hasMany(NivelInstitucion::class, 'institucion_id');
    }

    public function gradosInstitucion()
    {
        return $this->hasMany(GradoInstitucion::class, 'institucion_id');
    }

    // Relaciones academico.*
    public function asignaturas()
    {
        return $this->hasMany(\App\Models\Academico\Asignatura::class, 'institucion_id');
    }

    public function areasInstitucion()
    {
        return $this->hasMany(\App\Models\Academico\AreaInstitucion::class, 'institucion_id');
    }

    public function areasFormacion()
    {
        return $this->belongsToMany(
            \App\Models\Academico\AreaFormacion::class,
            'academico.areas_institucion',
            'institucion_id',
            'area_id'
        )->withPivot('nivel_id', 'estado')->withTimestamps();
    }

    public function competencias()
    {
        return $this->hasMany(\App\Models\Academico\Competencia::class, 'institucion_id');
    }

    public function logros()
    {
        return $this->hasMany(\App\Models\Academico\Logro::class, 'institucion_id');
    }

    // Relaciones inscripcion.*
    public function estudiantes()
    {
        return $this->hasMany(\App\Models\Inscripcion\Estudiante::class, 'institucion_id');
    }

    public function cursos()
    {
        return $this->hasMany(\App\Models\Inscripcion\Curso::class, 'institucion_id');
    }

    // Relaciones evaluacion.*
    public function periodos()
    {
        return $this->hasMany(\App\Models\Evaluacion\Periodo::class, 'institucion_id');
    }

    public function escalasCalificacion()
    {
        return $this->hasMany(\App\Models\Evaluacion\EscalaCalificacion::class, 'institucion_id');
    }

    public function actividades()
    {
        return $this->hasMany(\App\Models\Evaluacion\Actividad::class, 'institucion_id');
    }

    // Relaciones observador.*
    public function observaciones()
    {
        return $this->hasMany(\App\Models\Observador\Observacion::class, 'institucion_id');
    }

    public function asistencias()
    {
        return $this->hasMany(\App\Models\Observador\Asistencia::class, 'institucion_id');
    }

    // Relaciones mensajeria.*
    public function conversaciones()
    {
        return $this->hasMany(\App\Models\Mensajeria\Conversacion::class, 'institucion_id');
    }

    // Relaciones notificacion.*
    public function notificaciones()
    {
        return $this->hasMany(\App\Models\Notificacion\Notificacion::class, 'institucion_id');
    }

    // Relaciones horario.*
    public function franjasHorarias()
    {
        return $this->hasMany(\App\Models\Horario\FranjaHoraria::class, 'institucion_id');
    }

    public function horarios()
    {
        return $this->hasMany(\App\Models\Horario\Horario::class, 'institucion_id');
    }

    public function eventos()
    {
        return $this->hasMany(\App\Models\Horario\Evento::class, 'institucion_id');
    }

    // Relaciones auditoria.*
    public function logsActividad()
    {
        return $this->hasMany(\App\Models\Auditoria\LogActividad::class, 'institucion_id');
    }
}
