<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use withFaker;
    use RefreshDatabase;

    protected function setUp(): void {

        parent::setUp();

        User::factory()->create([
            'name' => $this->faker->name,
            'email' => 'example@example.com',
            'password' => 'Password1#',
            'is_admin' => false
        ]);

    }

    public function test_usuario_existente_puede_iniciar_sesion(): void
    {
        #teniendo
        $credenciales = [
            'email' => 'example@example.com',
            'password' => 'Password1#'
        ];

         # Haciendo
         $response = $this->postJson("{$this->apiVersion}/login", $credenciales);

         # Esperando
         $response->assertStatus(200);
         $response->assertJsonStructure([
             'data' => ['token']
         ]);
    }

    public function test_usuario_no_existente_no_puede_iniciar_sesion(): void
    {
        #teniendo
        $credenciales = [
            'email' => 'noexiste@example.com',
            'password' => 'Password1#'
        ];

         # Haciendo
         $response = $this->postJson("{$this->apiVersion}/login", $credenciales);

         # Esperando
         $response->assertStatus(401);
         $response->assertJsonFragment(['status' => 401, 'message' => 'Unauthorized' ]);
    }

    public function test_el_email_debe_ser_requerido(): void {
        #teniendo
        $credenciales = [
            'password' => 'Password1#'
        ];

         # Haciendo
         $response = $this->postJson("{$this->apiVersion}/login", $credenciales);

         # Esperando
         $response->assertStatus(422);
         $response->assertJsonStructure(['message', 'data', 'status' ,'errors' => ['email']]);
         $response->assertJsonFragment(['message' => 'The email field is required.']);
    }

    public function test_el_email_debe_ser_valido(): void {
        #teniendo
        $credenciales = [
            'email' => 'email',
            'password' => 'Password1#'
        ];

         # Haciendo
         $response = $this->postJson("{$this->apiVersion}/login", $credenciales);
         # Esperando
         $response->assertStatus(422);
         $response->assertJsonStructure(['message', 'data', 'status' ,'errors' => ['email']]);
         $response->assertJsonFragment(['message' => 'The email field must be a valid email address.']);
    }

    public function test_el_password_debe_ser_requerido(): void {
        #teniendo
        $credenciales = [
            'email' => 'example@example.com'
        ];

         # Haciendo
         $response = $this->postJson("{$this->apiVersion}/login", $credenciales);
         # Esperando
         $response->assertStatus(422);
         $response->assertJsonStructure(['message', 'data', 'status' ,'errors' => ['password']]);
         $response->assertJsonFragment(['message' => 'The password field is required.']);
    }

    public function test_el_password_debe_tener_ocho_o_mas_caracteres_y_cumplir_con_los_caracteres(): void {
        #teniendo
        $credenciales = [
            'email' => 'example@example.com',
            'password' => 'pass'
        ];

         # Haciendo
        $response = $this->postJson("{$this->apiVersion}/login", $credenciales);
         # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status' ,'errors' => ['password']]);
    }

}

