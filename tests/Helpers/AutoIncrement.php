<?php

namespace Allyson\ArtisanDomainContext\Tests\Helpers;

use Exception;
use Illuminate\Support\Facades\DB;

/**
 * @example
 *  $nextId = AutoIncrement::nextId(Post::class);
 *  $this->assertDatabaseHas('posts', [
 *      'id' => $nextId,
 *      'title' => 'New Post',
 *  ]);
 */
class AutoIncrement
{
    public static function nextId(string $modelClass)
    {
        $model = resolve($modelClass);

        if (! $model->getIncrementing()) {
            throw new Exception("{$model->getTable()} does not use an incrementing key.");
        }

        $result = DB::select("SHOW TABLE STATUS LIKE '{$model->getTable()}'");

        return $result[0]->Auto_increment;
    }
}
