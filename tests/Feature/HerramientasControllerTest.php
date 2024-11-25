<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HerramientasControllerTest extends TestCase
{
    private $_endpoint = '/herramientas';

    public function test_conteo_when_called_return_correct_view()
    {
        $this->get($this->_endpoint . '/conteo')
            ->assertOk()
            ->assertSee(['Billetes', 'Monedas', 'Centavos', 'Total']);
    }
}
