<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasUuids, Notifiable;

    protected $table = 'auth.usuarios';

    protected $fillable = [
        'tipo_documento_id',
        'numero_documento',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'email',
        'email_verificado_en',
        'password',
        'telefono',
        'celular',
        'direccion',
        'fecha_nacimiento',
        'genero',
        'municipio_id',
        'etnia_id',
        'discapacidad_id',
        'eps_id',
        'foto_url',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verificado_en' => 'datetime',
        'fecha_nacimiento' => 'date',
        'tipo_documento_id' => 'integer',
        'municipio_id' => 'integer',
        'etnia_id' => 'integer',
        'discapacidad_id' => 'integer',
        'eps_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accessors
    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->primer_nombre} {$this->segundo_nombre} {$this->primer_apellido} {$this->segundo_apellido}");
    }

    // Relaciones con ref.*
    public function tipoDocumento()
    {
        return $this->belongsTo(\App\Models\Ref\TipoDocumento::class, 'tipo_documento_id');
    }

    public function municipio()
    {
        return $this->belongsTo(\App\Models\Ref\Municipio::class, 'municipio_id');
    }

    public function etnia()
    {
        return $this->belongsTo(\App\Models\Ref\Etnia::class, 'etnia_id');
    }

    public function discapacidad()
    {
        return $this->belongsTo(\App\Models\Ref\Discapacidad::class, 'discapacidad_id');
    }

    public function eps()
    {
        return $this->belongsTo(\App\Models\Ref\Eps::class, 'eps_id');
    }

    // Relaciones con auth.*
    public function identidadesExternas()
    {
        return $this->hasMany(IdentidadExterna::class, 'usuario_id');
    }

    // Relaciones con core.*
    public function perfiles()
    {
        return $this->hasMany(\App\Models\Core\Perfil::class, 'usuario_id');
    }

    public function institucionesComoRector()
    {
        return $this->hasMany(\App\Models\Core\Institucion::class, 'rector_id');
    }

    public function institucionesComoManager()
    {
        return $this->hasMany(\App\Models\Core\Institucion::class, 'manager_id');
    }

    // Relaciones con inscripcion.*
    public function estudiante()
    {
        return $this->hasOne(\App\Models\Inscripcion\Estudiante::class, 'usuario_id');
    }

    public function acudientes()
    {
        return $this->hasMany(\App\Models\Inscripcion\Acudiente::class, 'usuario_id');
    }

    public function cursosComoDirector()
    {
        return $this->hasMany(\App\Models\Inscripcion\Curso::class, 'director_id');
    }

    public function asignaturasDocente()
    {
        return $this->hasMany(\App\Models\Inscripcion\DocenteAsignatura::class, 'usuario_id');
    }

    // Relaciones con evaluacion.*
    public function actividadesCreadas()
    {
        return $this->hasMany(\App\Models\Evaluacion\Actividad::class, 'docente_id');
    }

    // Relaciones con observador.*
    public function observacionesCreadas()
    {
        return $this->hasMany(\App\Models\Observador\Observacion::class, 'autor_id');
    }

    // Relaciones con mensajeria.*
    public function conversacionesCreadas()
    {
        return $this->hasMany(\App\Models\Mensajeria\Conversacion::class, 'creador_id');
    }

    public function participaciones()
    {
        return $this->hasMany(\App\Models\Mensajeria\Participante::class, 'usuario_id');
    }

    public function mensajesEnviados()
    {
        return $this->hasMany(\App\Models\Mensajeria\Mensaje::class, 'remitente_id');
    }

    // Relaciones con notificacion.*
    public function notificaciones()
    {
        return $this->hasMany(\App\Models\Notificacion\Notificacion::class, 'destinatario_id');
    }

    public function dispositivos()
    {
        return $this->hasMany(\App\Models\Notificacion\Dispositivo::class, 'usuario_id');
    }

    public function preferenciasNotificacion()
    {
        return $this->hasMany(\App\Models\Notificacion\Preferencia::class, 'usuario_id');
    }

    // Relaciones con horario.*
    public function horariosDocente()
    {
        return $this->hasMany(\App\Models\Horario\Horario::class, 'docente_id');
    }

    public function eventosCreados()
    {
        return $this->hasMany(\App\Models\Horario\Evento::class, 'creador_id');
    }

    // Relaciones con auditoria.*
    public function sesionesActivas()
    {
        return $this->hasMany(\App\Models\Auditoria\SesionActiva::class, 'usuario_id');
    }

    public function logsActividad()
    {
        return $this->hasMany(\App\Models\Auditoria\LogActividad::class, 'usuario_id');
    }

    public function registrosAcceso()
    {
        return $this->hasMany(\App\Models\Auditoria\RegistroAcceso::class, 'usuario_id');
    }
}
