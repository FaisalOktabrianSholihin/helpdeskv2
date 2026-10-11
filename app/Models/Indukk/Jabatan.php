<?php

namespace App\Models\Indukk;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $connection = 'indukk';

    protected $table = 'jabatans';

    protected $guarded = ['*'];
}
