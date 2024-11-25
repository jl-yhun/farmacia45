<?php

namespace App\Repositories;

use App\Tag;
use Illuminate\Database\Eloquent\Model;

interface ITagRepository
{
    public function all();
    public function create($input);
    public function link($type, $tageableId, $input);
    public function unlink(string $type, Model $tageable, Tag $tag);
    public function getByTageable(string $type, Model $tageable);
}
