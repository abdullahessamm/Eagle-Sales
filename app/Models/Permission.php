<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $incincrementing = false;
    protected $primaryKey = 'backoffice_user_id';
    /*
        access levels stracture:
        create|read|update|delete as valus 1 or 0 (1 has permession 0 hasn't it)
        the column value will be like this: 0000 or 1111 or 0100 ...etc.
    */
    protected $fillable = [
        'backoffice_user_id',
        'suppliers_access_level',
        'customers_access_level',
        'sellers_access_level',
        'categorys_access_level',
        'items_access_level',
        'backoffice_emps_access_level',
        'orders_access_level',
        'commissions_access_level',
        'journey_plan_access_level',
        'pricelists_access_level',
        'app_config_access'
    ];

    public function getBackofficeUser()
    {
        return $this->belongsTo(BackOfficeUser::class, 'backoffice_user_id', 'id')->first();
    }
}
