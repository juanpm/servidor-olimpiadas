<?php

use Illuminate\Database\Seeder;
use App\Carrera;

class CarreraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Carrera::create([
            'nombre' => 'Administracion de Empresas'
        ]);
        Carrera::create([
            'nombre' => 'Administracion Bancaria'
        ]);
        Carrera::create([
            'nombre' => 'Contabilidad'
        ]);
        Carrera::create([
            'nombre' => 'Administracion'
        ]);
        Carrera::create([
            'nombre' => 'Gastronomia y Arte Culinario'
        ]);
        Carrera::create([
            'nombre' => 'Computacion e Informatica'
        ]);
    }
}
