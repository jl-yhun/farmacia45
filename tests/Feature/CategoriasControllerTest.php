<?php

namespace Tests\Feature;

use App\Categoria;
use App\Repositories\CategoriasRepository;
use App\User;
use Database\Seeders\Categorias\TestingSeeder;
use Tests\TestCase;

class CategoriasControllerTest extends TestCase
{
    private $_endpoint = '/categorias';
    private $_user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestingSeeder::class);
        $this->_user = User::find(1);
    }

    public function test_index_when_called_return_list_of_categories()
    {
        $this->actingAs($this->_user);
        $response = $this->get($this->_endpoint);

        $response->assertStatus(200);
        $response->assertSee(['CATEGORIA XYZ', 'Nombre', 'Admite', 'Tasa IVA']);
    }

    public function test_index_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $this->get($this->_endpoint)
            ->assertStatus(403)
            ->assertSee('No autorizado');
    }

    public function test_create_when_called_return_view()
    {
        $this->actingAs($this->_user);

        $this->get($this->_endpoint . '/create')
            ->assertStatus(200)
            ->assertSee('Nombre')
            ->assertSee('Admite')
            ->assertSee('Tasa IVA');
    }

    public function test_create_when_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $this->get($this->_endpoint . '/create')
            ->assertStatus(403)
            ->assertSee('No autorizado.');
    }

    public function test_store_when_called_save_category_correctly_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $newCategory = [
            'nombre' => 'ÑANdÚ',
            'admite' => 'NINGUNO',
            'tasa_iva' => .16
        ];

        $this->post($this->_endpoint, $newCategory)->assertStatus(200);

        $this->assertDatabaseHas('log', [
            'modulo' => CategoriasRepository::class,
            'despues' => json_encode($newCategory)
        ]);

        $newCategory['nombre'] = 'NANDU';
        $this->assertDatabaseHas('categorias', $newCategory);
    }

    public function test_store_when_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $newCategory = [
            'nombre' => fake()->name(),
            'admite' => 'NINGUNO',
            'tasa_iva' => 0
        ];

        $this->post($this->_endpoint, $newCategory)
            ->assertStatus(403)
            ->assertSee('No autorizado');
    }

    public function test_store_when_error_return_error_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $newCategory = [
            'nombre' => 'CATEGORÍA XYZ',
            'admite' => 'NINGUNO',
            'tasa_iva' => .16
        ];

        $this->post($this->_endpoint, $newCategory)
            ->assertStatus(302)
            ->assertSessionHasErrors('general');

        $this->assertDatabaseHas('log', [
            'modulo' => CategoriasRepository::class,
            'antes' => json_encode($newCategory)
        ]);
        $this->assertDatabaseMissing('log', [
            'modulo' => CategoriasRepository::class,
            'despues' => json_encode($newCategory)
        ]);
    }

    public function test_store_when_no_valid_no_saved_return_error_message()
    {
        $this->actingAs($this->_user);

        $newCategory = [
            'nombre' => fake()->name(),
            'admite' => 'NINGUNO'
        ];

        $this->post($this->_endpoint, $newCategory)
            ->assertStatus(302)
            ->assertSessionHasErrors('tasa_iva')
            ->assertSessionHasInput(['admite', 'nombre']);
    }

    public function test_edit_when_called_return_view()
    {
        $this->actingAs($this->_user);

        $category = Categoria::find(1);

        $this->get($this->_endpoint . '/1/edit')
            ->assertOk()
            ->assertSee([$category->nombre, $category->admite, $category->tasa_iva * 100]);
    }

    public function test_edit_when_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $this->get($this->_endpoint . '/1/edit')
            ->assertStatus(403)
            ->assertSee('No autorizado.');
    }

    public function test_update_when_called_save_category_correctly_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $updatedCategory = [
            'nombre' => fake()->name(),
            'admite' => 'CAMBIO',
            'tasa_iva' => .16
        ];

        $this->put($this->_endpoint . '/3', $updatedCategory)
            ->assertStatus(200);

        $this->assertDatabaseHas('categorias', $updatedCategory);
        $this->assertDatabaseHas('log', [
            'modulo' => CategoriasRepository::class,
            'despues' => json_encode($updatedCategory)
        ]);
    }

    public function test_update_when_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $updatedCategory = [
            'nombre' => fake()->name(),
            'admite' => 'CAMBIO',
            'tasa_iva' => 0
        ];

        $this->put($this->_endpoint . '/3', $updatedCategory)
            ->assertStatus(403)
            ->assertSee('No autorizado.');
    }

    public function test_update_when_error_return_error_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $updatedCategory = [
            'nombre' => 'CATEGORÍA XYZ',
            'admite' => 'CAMBIO',
            'tasa_iva' => 0
        ];

        $this->put($this->_endpoint . '/3', $updatedCategory)
            ->assertStatus(302)
            ->assertSessionHasErrors('general');

        $this->assertDatabaseHas('log', [
            'modulo' => CategoriasRepository::class,
            'despues' => json_encode($updatedCategory)
        ]);
        $this->assertDatabaseMissing('log', [
            'modulo' => CategoriasRepository::class,
            'antes' => json_encode($updatedCategory)
        ]);
    }

    public function test_update_when_invalid_return_error_message()
    {
        $this->actingAs($this->_user);

        $updatedCategory = [
            'admite' => 'CAMBIO',
            'nombre' => fake()->name()
        ];

        $this->put($this->_endpoint . '/3', $updatedCategory)
            ->assertStatus(302)
            ->assertSessionHasErrors('tasa_iva')
            ->assertSessionHasInput(['nombre', 'admite']);
    }

    public function test_destroy_when_called_soft_deleted_correctly()
    {
        $this->actingAs($this->_user);

        $this->delete($this->_endpoint . '/3')
            ->assertStatus(302);

        $this->assertSoftDeleted('categorias', ['id' => 3]);
    }

    public function test_destroy_when_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $this->delete($this->_endpoint . '/3')
            ->assertStatus(403)
            ->assertSee('No autorizado');
    }
}
