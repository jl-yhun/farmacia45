<?php

namespace App\Console\Commands;

use App\Helpers\Logger;
use App\PermisoTemp;
use App\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class RevisarPermisos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ptv:revisar-permisos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        for ($t = 0; $t < 90; $t++) {
            $permisosTemp = PermisoTemp::where("expiro", 0)->get();
            foreach ($permisosTemp as $permisoTemp) {
                try {
                    // Si el permiso expiró
                    if ($permisoTemp->expiracion <= date("Y-m-d H:i:s")) {
                        $permiso = Permission::findById($permisoTemp->permiso_id);
                        $usuario = User::find($permisoTemp->usuario_id);
                        $usuario->revokePermissionTo($permiso);
                        $permisoTemp->expiro = 1;
                        $permisoTemp->save();
                    }
                } catch (Exception $e) {
                    Logger::error(
                        $permisoTemp->permiso_id,
                        "No se pudo quitar permiso",
                        $permisosTemp->usuario_id,
                        null,
                        null,
                        "CRON " . $this->signature,
                        $e
                    );
                }
            }
            sleep(10);
        }
    }
}
