<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Settings
 * @package App
 *
 * @property string key
 * @property string value
 */
class Settings extends Model
{
    public $timestamps = false;
    public $primaryKey = "key";
    public $incrementing = false;
    protected $fillable = ['key','value'];
}
