<?php

namespace App\Models;

use App\Models\Model;
use App\Models\UrlCheck;

class Url extends Model
{
    protected static string $table = 'urls';

    public function checks()
    {
        return $this->hasMany(UrlCheck::class, 'url_id');
    }

    public function lastCheck()
    {
        return $this->hasOne(UrlCheck::class, 'url_id')->orderBy('id', 'DESC');
    }
}
