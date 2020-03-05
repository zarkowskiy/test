<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Places
 * @package App\Models
 *
 * @property int idPlace
 * @property int idCity
 * @property string names
 * @property string address
 * @property string description
 */
class Places extends Model
{
    public $timestamps = false;
    protected $primaryKey = "idPlace";

    /**
     * @return HasOne
     */
    public function city(){
        return $this->hasOne(Cities::class, 'idCity','idCity');
    }

    /**
     * @return HasOne
     */
    public function schedule(){
        return $this->hasOne(Schedules::class, 'idPlace','idPlace');
    }
}
