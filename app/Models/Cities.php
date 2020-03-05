<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Cities
 * @package App\Models
 *
 * @property int idCity
 * @property string name
 */
class Cities extends Model
{
    public $timestamps = false;
    protected $primaryKey = "idCity";

    /**
     * @return HasMany
     */
    public function users(){
        return $this->hasMany(TelegramUsers::class, 'idCity','idCity');
    }

    /**
     * @return HasMany
     */
    public function places(){
        return $this->hasMany(Places::class, 'idCity','idCity');
    }
}
