<?php

namespace App\Console\Commands;

use App\Categoria;
use App\Producto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Importar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ptv:importar {modulo=all}';

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
        $modulo = $this->argument('modulo');
        switch (strtolower($modulo)) {
            case "categorias":
                $this->categorias();
                break;
            case "productos":
                $this->productos();
                break;
            case "all":
                $this->categorias();
                $this->productos();
                break;
        }
    }
    public function categorias()
    {
        $categorias = DB::connection("mysql2")->table("categorias")->get();
        foreach ($categorias as $categoria) {
            Categoria::updateOrCreate(["nombre" => $categoria->name], ["admite" => "NINGUNO"]);
        }
    }
    public function productos()
    {
        $productos = DB::connection("mysql2")->table("inventario")->get();
        foreach ($productos as $producto) {
            $prod = Producto::find((int)$producto->codigo);
            $cate = DB::connection("mysql2")->table("categorias")->where("id", $producto->category)->first();
            $catego = Categoria::where("nombre", $cate->name)->first();

            if (!$prod) {
                $prod = new Producto();
                $prod->incrementing = false;
                $prod->id = (int)$producto->codigo;
            }
            $prod->nombre = $producto->description;
            $prod->compra = $producto->priceunit;
            $prod->venta = $producto->price;
            $prod->categoria_id = $catego->id;
            $prod->stock = $producto->units;
            $prod->save();
            $prod->incrementing = true;
        }
    }
}
