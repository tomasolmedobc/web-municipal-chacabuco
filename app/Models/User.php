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

#[Fillable(['name', 'apellido', 'email', 'password', 'rol', 'modulos'])]
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
        'baile_egresados'    => 'Baile de Egresados',
        'habilitaciones'     => 'Habilitaciones',
        'obras_particulares' => 'Obras Particulares',
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
