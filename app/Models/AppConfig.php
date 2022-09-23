<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppConfig extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'key', 'value', 'updated_by', 'updated_at'
    ];

    protected $dates = [
        'updated_at'
    ];

    protected static function getConfig(string $moduleName, string $key)
    {
        $conf = self::where('key', $key)->first();
        if (! $conf)
            return config("eagle-sales.$moduleName.$key");

        return $conf->value;
    }

    /**
     * @return boolean
     */
    public static function sellerInvitationRewardEnabled()
    {
        return (bool) self::getConfig('users', 'sellers_invitation_reward_enabled');
    }

    /**
     * @return integer|null
     */
    public static function sellerInvitationRewardAmount()
    {
        return self::getConfig('users', 'sellers_invitation_reward_amount');
    }

    /**
     * @return integer|null
     */
    public static function sellersInvitationRewardTax()
    {
        return self::getConfig('users', 'sellers_invitation_reward_tax');
    }

    /**
     * @return boolean
     */
    public static function autoApproveCustomersEnabled()
    {
        return self::getConfig('users', 'auto_approve_customers');
    }

    /**
     * @return boolean
     */
    public static function autoApproveFreelancerSellersEnabled()
    {
        return (bool) self::getConfig('users', 'auto_approve_freelancer_sellers');
    }

    /**
     * @return boolean
     */
    public static function autoApproveOnlineClientsEnabled()
    {
        return (bool) self::getConfig('users', 'auto_approve_online_clients');
    }

    /**
     * @return boolean
     */
    public static function sendEmailToUserAfterApprovalReplyEnabled()
    {
        return (bool) self::getConfig('users', 'send_email_to_user_after_approval_reply');
    }

    /**
     * @return boolean
     */
    public static function sendSmsToUserAfterApprovalReplyEnabled()
    {
        return (bool) self::getConfig('users', 'send_sms_to_user_after_approval_reply');
    }

    /**
     * @return boolean
     */
    public static function autoApproveItemsEnabled()
    {
        return (bool) self::getConfig('items', 'auto_approve_items');
    }

    /**
     * @return boolean
     */
    public static function sendEmailToSupplierWhenItemApprovalReplyEnabled()
    {
        return (bool) self::getConfig('items', 'send_email_to_supplier_when_item_approval_reply');
    }

    /**
     * @return boolean
     */
    public static function sendSmsToSupplierWhenItemApprovalReplyEnabled()
    {
        return (bool) self::getConfig('items', 'send_sms_to_supplier_when_item_approval_reply');
    }

    /**
     * @return boolean
     */
    public static function ItemsCommentsEnabled()
    {
        return (bool) self::getConfig('items', 'enable_comments_on_items');
    }

    /**
     * @return boolean
     */
    public static function ItemsRatingsEnabled()
    {
        return (bool) self::getConfig('items', 'enable_ratings_on_items');
    }

    /**
     * @return boolean
     */
    public static function ItemsPromotionsEnabled()
    {
        return (bool) self::getConfig('items', 'enable_promotions_on_items');
    }

    /**
     * @return integer|null
     */
    public static function sellersCommission()
    {
        return self::getConfig('orders', 'sellers_commission');
    }

    /**
     * @return integer|null
     */
    public static function ourCommission()
    {
        return self::getConfig('orders', 'our_commission');
    }

    /**
     * @return boolean
     */
    public static function sendEmailToUsersWhenOrderStateChangedEnabled()
    {
        return (bool) self::getConfig('orders', 'send_email_to_users_when_order_state_changed');
    }

    /**
     * @return boolean
     */
    public static function sendSmsToUsersWhenOrderStateChangedEnabled()
    {
        return (bool) self::getConfig('orders', 'send_sms_to_users_when_order_state_changed');
    }

    /**
     * @return boolean
     */
    public static function orderCanBeCancelledAfterApproved()
    {
        return (bool) self::getConfig('orders', 'order_can_be_cancelled_after_approved');
    }


    /**
     * @return integer|null
     */
    public static function maxCommissionsNumForSellers()
    {
        return self::getConfig('orders', 'max_commissions_num_for_sellers');
    }

    /**
     * @return float
     */
    public static function journyPlanDistanceToMarkVisit()
    {
        return self::getConfig('journey_plan', 'journy_plan_distance_to_mark_visit');
    }
}
