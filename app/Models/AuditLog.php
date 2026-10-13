<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_nombre',
        'accion',
        'modelo',
        'modelo_id',
        'descripcion',
        'ip',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Registra eventos de acceso (login, logout, intentos fallidos). A diferencia de registrar(),
     * el usuario se pasa explícito porque en un intento fallido todavía no hay sesión.
     */
    public static function registrarAcceso(string $accion, string $descripcion, ?User $usuario = null): void
    {
        static::create([
            'user_id'     => $usuario?->id,
            'user_nombre' => $usuario?->name,
            'accion'      => $accion,
            'modelo'      => 'Acceso',
            'modelo_id'   => $usuario?->id,
            'descripcion' => mb_substr($descripcion, 0, 255),
            'ip'          => Request::ip(),
        ]);
    }

    public static function registrar(string $accion, string $modelo, ?int $modeloId, string $descripcion): void
    {
        static::create([
            'user_id'     => Auth::id(),
            'user_nombre' => Auth::user()?->name,
            'accion'      => $accion,
            'modelo'      => $modelo,
            'modelo_id'   => $modeloId,
            'descripcion' => $descripcion,
            'ip'          => Request::ip(),
        ]);
    }
}
