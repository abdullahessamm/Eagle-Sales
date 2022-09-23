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
        'statistics_screen_access',
        'app_config_access'
    ];

    public function getBackofficeUser()
    {
        return $this->belongsTo(BackOfficeUser::class, 'backoffice_user_id', 'id')->first();
    }

    public function getUserIdAttribute()
    {
        return $this->getBackofficeUser()->user_id;
    }

    /**
     *
     * @param string $ability - the ability name in [create, read, update, delete]
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public static function getUsersCan(string $ability, \Illuminate\Database\Eloquent\Model $model): array | null
    {
        $abilityPatterns = [
            'create' => '1...',
            'read'   => '.1..',
            'update' => '..1.',
            'delete' => '...1'
        ];

        $abilityPattern = $abilityPatterns[$ability];
        $permissionColNames = [
            'supplier' => 'suppliers_access_level',
            'customer' => 'customers_access_level',
            'seller'   => 'sellers_access_level',
            'category' => 'categorys_access_level',
            'item'     => 'items_access_level',
            'backofficeuser' => 'backoffice_emps_access_level',
            'order' => 'orders_access_level',
            'commission' => 'commissions_access_level',
            'journeyplan' => 'journey_plan_access_level',
            'pricelist' => 'pricelists_access_level'
        ];

        $model = strtolower(class_basename($model));
        $permissionColName = $permissionColNames[$model];

        if (! $permissionColName)
            return null;

        $permissionsRows = self::where($permissionColName, 'regexp', $abilityPattern)->get();
        
        $users = [];
        foreach ($permissionsRows as $permissionRow)
            $users[] = $permissionRow->getBackofficeUser()->getUser();
        
        return $users;
    }

    /**
     * Get the users that have the ability to make some action on some model.
     *
     * @param string $ability
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public static function getUsersIdsCan(string $ability, \Illuminate\Database\Eloquent\Model $model): array | null
    {
        $users = collect(self::getUsersCan($ability, $model));
        return $users->map(function($user) {
            return $user->id;
        })->toArray();
    }

    public function hasAccessToStatistics()
    {
        return (bool) $this->statistics_screen_access;
    }
}
