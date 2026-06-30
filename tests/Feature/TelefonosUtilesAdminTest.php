<?php

namespace Tests\Feature;

use App\Models\TelefonoUtil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TelefonosUtilesAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['rol' => 'admin'])->save();
        return $user;
    }

    public function test_admin_can_create_entry(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.telefonos-utiles.store'), [
            'nombre'    => 'Defensa Civil',
            'categoria' => 'Seguridad',
            'telefono'  => '103',
            'estado'    => 'visible',
            'orden'     => 0,
        ]);

        $response->assertRedirect(route('admin.telefonos-utiles.index'));

        $this->assertDatabaseHas('telefonos_utiles', [
            'nombre'    => 'Defensa Civil',
            'categoria' => 'Seguridad',
            'telefono'  => '103',
        ]);
    }

    public function test_admin_can_update_entry(): void
    {
        $item = TelefonoUtil::create([
            'nombre'   => 'Nombre original',
            'categoria' => 'Salud',
            'estado'   => 'visible',
            'orden'    => 0,
        ]);

        $response = $this->actingAs($this->admin())->put(route('admin.telefonos-utiles.update', $item), [
            'nombre'    => 'Nombre actualizado',
            'categoria' => 'Salud',
            'estado'    => 'visible',
            'orden'     => 1,
        ]);

        $response->assertRedirect(route('admin.telefonos-utiles.index'));

        $this->assertDatabaseHas('telefonos_utiles', ['nombre' => 'Nombre actualizado']);
        $this->assertDatabaseMissing('telefonos_utiles', ['nombre' => 'Nombre original']);
    }

    public function test_admin_can_delete_entry(): void
    {
        $item = TelefonoUtil::create([
            'nombre'   => 'A eliminar',
            'categoria' => 'Deportes',
            'estado'   => 'visible',
            'orden'    => 0,
        ]);

        $response = $this->actingAs($this->admin())->delete(route('admin.telefonos-utiles.destroy', $item));

        $response->assertRedirect(route('admin.telefonos-utiles.index'));
        $this->assertDatabaseMissing('telefonos_utiles', ['id' => $item->id]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.telefonos-utiles.store'), []);

        $response->assertSessionHasErrors(['nombre', 'estado']);
    }

    public function test_guest_cannot_access_admin(): void
    {
        $response = $this->get(route('admin.telefonos-utiles.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_index_shows_entries(): void
    {
        TelefonoUtil::create(['nombre' => 'Bomberos', 'categoria' => 'Seguridad', 'estado' => 'visible', 'orden' => 0]);

        $response = $this->actingAs($this->admin())->get(route('admin.telefonos-utiles.index'));

        $response->assertOk();
        $response->assertSee('Bomberos');
    }
}
