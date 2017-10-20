<?php

namespace Pimlie\DataTables\Tests\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Heart extends Eloquent
{
    protected $connection = 'mongodb';

    static protected $unguarded = true;

    public function user()
    {
        return $this->hasOne(User::class);
    }
}