<?php

namespace Stafe\OptimisticLocking\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Stafe\OptimisticLocking\Traits\OptimisticLocking;

class Post extends Model
{
    use OptimisticLocking;

    protected $fillable = ['title'];

    public $timestamps = false;
}
