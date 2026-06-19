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
