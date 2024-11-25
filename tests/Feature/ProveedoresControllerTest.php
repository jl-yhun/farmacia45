<?php

namespace Tests\Feature;

use App\Proveedor;
use App\User;
use Database\Seeders\Proveedores\TestingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProveedoresControllerTest extends TestCase
{
    private $_user;
    private $_endpoint = '/api/proveedores';
    private $_proveedores;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestingSeeder::class);
        $this->_user = User::find(1);
        $this->_proveedores = Proveedor::all()->toArray();
    }

    public function test_index_when_called_return_correct_json()
    {
        $this->actingAs($this->_user);

        $this->get($this->_endpoint)
            ->assertOk()
            ->assertJsonPath('estado', true)
            ->assertJsonPath('data', $this->_proveedores);
    }

    public function test_index_when_no_authorized_return_correct_result()
    {
        $user = User::find(2); // No Auth
        $this->actingAs($user);

        $this->get($this->_endpoint)
            ->assertForbidden();
    }

    public function test_index_when_no_authenticated_return_correct_result()
    {
        $this->get($this->_endpoint)
            ->assertRedirectToRoute('login');
    }
}
