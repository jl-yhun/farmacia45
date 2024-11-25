<?php

namespace App\Repositories;

use App\Helpers\LoggerBuilder;
use App\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TagRepository implements ITagRepository
{
    private $_logger;

    public function __construct(LoggerBuilder $logger)
    {
        $this->_logger = $logger;
    }

    public function all() {
        return DB::table('tags')->get();
    }

    public function create($input)
    {
        try {
            DB::beginTransaction();

            DB::table('tags')->insert(['usuario_id' => Auth::user()->id, ...$input]);
            DB::commit();
            $this->_logger
                ->success()
                ->description($this::class . '::create finished')
                ->user_id(Auth::user()->id)
                ->module($this::class)
                ->after(json_encode($input))
                ->log();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->_logger
                ->error()
                ->exception($th)
                ->description($this::class . '::create finished with error')
                ->user_id(Auth::user()->id)
                ->module($this::class)
                ->after(json_encode($input))
                ->log();
        }

        return false;
    }

    private function createOrRetrieve($input): int
    {
        $tag = $this->showByName($input['nombre']);

        if (!$tag) {
            $this->create($input);
            $tag = $this->showByName($input['nombre']);
        }

        return $tag->id;
    }

    public function link($type, $tageableId, $input)
    {
        try {
            DB::beginTransaction();

            $tagId = $this->createOrRetrieve($input);

            DB::table('tags_models')->insert([
                'tageable_id' => $tageableId,
                'tag_id' => $tagId,
                'tageable_type' => $type
            ]);

            DB::commit();

            $this->_logger
                ->success()
                ->description($this::class . '::link finished')
                ->user_id(Auth::user()->id)
                ->module($this::class)
                ->after(json_encode($input))
                ->log();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->_logger
                ->error()
                ->exception($th)
                ->description($this::class . '::link finished with error')
                ->user_id(Auth::user()->id)
                ->module($this::class)
                ->after(json_encode($input))
                ->log();
        }

        return false;
    }

    public function unlink(string $type, Model $tageable, Tag $tag)
    {
        try {
            DB::beginTransaction();

            DB::table('tags_models')->where([
                'tageable_id' => $tageable->id,
                'tag_id' => $tag->id,
                'tageable_type' => $type
            ])->delete();

            DB::commit();

            $this->_logger
                ->success()
                ->description($this::class . '::unlink finished for tageable ' . $tageable->id)
                ->user_id(Auth::user()->id)
                ->module($this::class)
                ->link_id($tag->id)
                ->log();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->_logger
                ->error()
                ->exception($th)
                ->description($this::class . '::unlink finished with error for tageable ' . $tageable->id)
                ->user_id(Auth::user()->id)
                ->module($this::class)
                ->link_id($tag->id)
                ->log();
        }

        return false;
    }

    public function show(int $id)
    {
        return DB::table('tags')->find($id);
    }

    public function getByTageable(string $type, Model $tageable)
    {
        try {
            return DB::select(
                <<<EOT
                SELECT t.nombre, t.id FROM tags_models tm 
                INNER JOIN tags t ON t.id = tm.tag_id 
                WHERE tm.tageable_id = :tageableId 
                AND tm.tageable_type = :type
                EOT,
                [
                    'tageableId' => $tageable->id,
                    'type' => $type
                ]
            );
        } catch (\Throwable $th) {
            $this->_logger
                ->error()
                ->exception($th)
                ->description($this::class . '::getByTageable finished with error')
                ->user_id(Auth::user()->id)
                ->module($this::class)
                ->link_id($tageable->id)
                ->log();

            throw $th;
        }
    }


    public function showByName($nombre)
    {
        return DB::table('tags')
            ->where('nombre', $nombre)
            ->first();
    }
}
