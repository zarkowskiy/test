<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class TelegramUsers
 * @package App\Models
 *
 * @property int idTelegramUser
 * @property int idCity
 * @property string username
 * @property string lastname
 * @property string firstname
 */
class TelegramUsers extends Model
{
    public $timestamps = false;
    protected $primaryKey = "idTelegramUser";

    /**
     * @return HasOne
     */
    public function city(){
        return $this->hasOne(Cities::class, 'idCity', 'idCity');
    }
}
