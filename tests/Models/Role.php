<?php

namespace Pimlie\DataTables\Tests\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Role extends Eloquent
{
    protected $connection = 'mongodb';

    static protected $unguarded = true;

    public function user()
    {
        return $this->belongsTo('User');
    }
}