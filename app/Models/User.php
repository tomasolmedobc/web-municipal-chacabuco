<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Fillable(['name', 'apellido', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    const MODULOS = [
        'noticias'           => 'Noticias',
        'turismo'            => 'Turismo',
        'telefonos_utiles'   => 'Teléfonos Útiles',
        'gobierno_abierto'   => 'Gobierno Abierto',
        'habilitaciones'     => 'Habilitaciones',
        'obras_particulares' => 'Obras Particulares',
        'proveedores'        => 'Compras y Proveedores',
        'tasas'              => 'Tasas Municipales',
        'recaudacion'        => 'Recaudación',
        'popup'              => 'Popup anuncio',
        'carnet_conducir'    => 'Carnet de Conducir',
        'auditoria'          => 'Auditoría',
    ];

    public function canAccess(string $module): bool
    {
        if ($this->rol === 'admin') {
            return true;
        }

        $modulos = $this->modulos ?? [];
        return in_array($module, $modulos);
    }

    /**
     * Cierra las sesiones abiertas del usuario (por ejemplo tras cambiarle la contraseña)
     * e invalida su cookie de "recordarme". Con $excepto se conserva una sesión, normalmente la actual.
     */
    public function cerrarSesiones(?string $excepto = null): void
    {
        $this->forceFill(['remember_token' => Str::random(60)])->save();

        if (config('session.driver') !== 'database') {
            return;
        }

        $consulta = DB::connection(config('session.connection'))
            ->table(config('session.table', 'sessions'))
            ->where('user_id', $this->getKey());

        if ($excepto) {
            $consulta->where('id', '!=', $excepto);
        }

        $consulta->delete();
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->name . ' ' . $this->apellido);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'modulos'           => 'array',
        ];
    }
}
