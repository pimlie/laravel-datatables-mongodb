<?php

namespace Pimlie\DataTables\Tests\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class User extends Eloquent
{
    protected $connection = 'mongodb';

    static protected $unguarded = true;

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function heart()
    {
        return $this->hasOne(Heart::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}