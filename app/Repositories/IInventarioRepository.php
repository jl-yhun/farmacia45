<?php

namespace App\Repositories;

interface IInventarioRepository
{
    public function create($data);
    public function get();
    public function show($codigo_barras);
    public function destroy($codigo_barras);
    public function update($data, $codigo_barras);

    public function diff();
    public function finish();
}
