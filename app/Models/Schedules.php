<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Schedules
 * @package App\Models
 *
 * @property int idSchedule
 * @property int idPlace
 * @property string scheduledJSON
 */
class Schedules extends Model
{
    public $timestamps = false;
    protected $primaryKey = "idSchedule";
    protected $fillable = [
        "idPlace",
        "scheduledJSON"
    ];

    protected $casts = [
        'scheduledJSON' => 'json'
    ];
    /**
     * @return HasOne
     */
    public function place(){
        return $this->hasOne(Places::class, 'idPlace','idPlace');
    }
}
