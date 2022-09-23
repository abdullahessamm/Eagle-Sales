<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersPlace extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coords',
        'country',
        'country_ar',
        'country_code',
        'governorate',
        'governorate_ar',
        'zone',
        'zone_ar',
        'city',
        'city_ar',
        'street',
        'street_ar',
        'building_no',
        'is_primary',
    ];

    protected $hidden = [
        'user_id',
        'coords',
        'zone',
        'zone_ar',
        'city',
        'city_ar',
        'street',
        'street_ar',
        'building_no',
        'is_primary',
        'created_at',
        'updated_at',
    ];

    public function orders()
    {
        $foreign = $this->user()->first()->isSupplier() ? 'shipping_address_id' : 'billing_address_id';
        return $this->hasMany(Order::class, $foreign, 'id');
    }

    public function showHiddens()
    {
        $this->makeVisible($this->hidden);
        return $this;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getUser()
    {
        return $this->user()->get()->first();
    }

    public function getFullAddress($lang = 'en')
    {
        $address = '';
        if ($lang == 'en') {
            
            if ($this->building_no) {
                $address .= ', ' . $this->building_no;
            }

            if ($this->street) {
                $address .= ', ' . $this->street;
            }

            if ($this->zone) {
                $address .= ', ' . $this->zone;
            }

            if ($this->city) {
                $address .= ', ' . $this->city;
            }

            if ($this->governorate) {
                $address .= ', ' . $this->governorate;
            }

            if ($this->country) {
                $address .= ', ' . $this->country;
            }

        } else {
                
                if ($this->building_no) {
                    $address .= ', ' . $this->building_no;
                }
    
                if ($this->street_ar) {
                    $address .= ', ' . $this->street_ar;
                }
    
                if ($this->zone_ar) {
                    $address .= ', ' . $this->zone_ar;
                }
    
                if ($this->city_ar) {
                    $address .= ', ' . $this->city_ar;
                }
    
                if ($this->governorate_ar) {
                    $address .= ', ' . $this->governorate_ar;
                }
    
                if ($this->country_ar) {
                    $address .= ', ' . $this->country_ar;
                }    
        }
        return trim($address, ', ');
    }

    public function setPrimary()
    {
        $primaryPlace = $this->where('user_id', $this->user_id)->where('is_primary', 1)->first();
        if ($primaryPlace) {
            $primaryPlace->is_primary = false;
            $primaryPlace->save();
        }
        $this->is_primary = true;
    }
}
