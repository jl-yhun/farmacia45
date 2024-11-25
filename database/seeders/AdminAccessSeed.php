<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminAccessSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedPermissions();
        $this->seedRoles();
    }

    private function seedRoles()
    {
        $this->seedAdminRolAndUser();
        $this->seedManagerRol();
        $this->seedVendedorRol();
    }

    private function seedAdminRolAndUser()
    {
        $permisosArray = $this->getPermisosAdmin();
        $permisos = Permission::whereIn('name', array_keys($permisosArray))->get();

        $role = Role::updateOrCreate(['name' => 'Admin']);
        $role->syncPermissions($permisos);

        $user = User::updateOrCreate(
            ['id' => 1],
            ['name' => 'admin', 'email' => 'admin', 'password' => '$$//Atenea12$$//']
        );

        $user->assignRole($role);
    }

    private function seedManagerRol()
    {
        $permisosArray = $this->getPermisosManager();
        $permisos = Permission::whereIn('name', array_keys($permisosArray))->get();

        $role = Role::updateOrCreate(['name' => 'Manager']);
        $role->syncPermissions($permisos);
    }

    private function seedVendedorRol()
    {
        $permisosArray = $this->getPermisosVendedor();
        $permisos = Permission::whereIn('name', array_keys($permisosArray))->get();

        $role = Role::updateOrCreate(['name' => 'Vendedor']);
        $role->syncPermissions($permisos);
    }

    private function seedPermissions()
    {
        $permisos = $this->getPermisosAdmin();

        foreach ($permisos as $key => $permiso) {
            Permission::updateOrCreate(
                ['name' => $key],
                ['friendly_name' => $permiso]
            );
        }
    }

    private function getPermisosAdmin(): array
    {
        return [
            // Lectura
            'productos.view' => 'Ver productos',
            'perdidas.view' => 'Ver pérdidas',
            'gastos.view' => 'Ver gastos',
            'usuarios.view' => 'Ver usuarios',
            'categorias.view' => 'Ver categorías',
            'aperturas-caja.view' => 'Ver aperturas caja',
            'ordenes-compra.view' => 'Ver órdenes de compra',
            'ordenes-compra-faltantes.view' => 'Ver faltantes',
            'apartados.view' => 'Ver apartados',
            'similares.view' => 'Ver productos similares',
            'proveedores.view' => 'Ver proveedores',
            // Escritura
            'productos.creation' => 'Registrar productos',
            'perdidas.creation' => 'Registrar pérdidas',
            'gastos.creation' => 'Registrar gastos',
            'usuarios.creation' => 'Registrar usuarios',
            'categorias.creation' => 'Registrar categorías',
            'apartados.creation' => 'Registrar apartados',
            'similares.creation' => 'Registrar productos similares',
            'productos-proveedores.creation' => 'Ligar productos con proveedores',
            'ordenes-compra.creation' => 'Registrar órdenes de compra',
            // Actualización
            'productos.update' => 'Modificar productos',
            'usuarios.update' => 'Registrar usuarios',
            'categorias.update' => 'Registrar categorías',
            'productos-proveedores.update' => 'Actualizar relación de productos con proveedores',
            'ordenes-compra.update' => 'Actualizar órdenes de compra',
            // Remover
            'gastos.delete' => 'Eliminar gastos',
            'categorias.delete' => 'Eliminar categorías',
            'usuarios.delete' => 'Eliminar usuarios',
            'productos.delete' => 'Eliminar productos',
            'productos-proveedores.delete' => 'Eliminar relación de productos con proveedores',
            'ventas.delete' => 'Cancelar ventas'
        ];
    }

    private function getPermisosManager(): array
    {
        $allPermisos = $this->getPermisosAdmin();
        unset($allPermisos['aperturas-caja.view']);
        unset($allPermisos['categorias.creation']);
        unset($allPermisos['categorias.update']);
        unset($allPermisos['categorias.delete']);
        unset($allPermisos['gastos.delete']);
        unset($allPermisos['usuarios.view']);
        unset($allPermisos['usuarios.creation']);
        unset($allPermisos['usuarios.update']);
        unset($allPermisos['usuarios.delete']);
        unset($allPermisos['productos-proveedores.delete']);
        unset($allPermisos['ventas.delete']);

        return $allPermisos;
    }

    private function getPermisosVendedor(): array
    {
        $permisosManager = $this->getPermisosManager();
        unset($permisosManager['apartados.view']);
        unset($permisosManager['productos.delete']);
        unset($permisosManager['perdidas.view']);

        return $permisosManager;
    }
}
