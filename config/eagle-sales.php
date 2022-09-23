<?php

return [
    "users" => [
        "sellers_invitation_reward_enabled"       => false,
        "sellers_invitation_reward_amount"        => 0,
        "sellers_invitation_reward_tax"           => 0,
        "auto_approve_customers"                  => false,
        "auto_approve_freelancer_sellers"         => false,
        "auto_approve_online_clients"             => true,
        "send_email_to_user_after_approval_reply" => true,
        "send_sms_to_user_after_approval_reply"   => false,
    ],
    "items" => [
        "auto_approve_items"                              => false,
        "send_email_to_supplier_when_item_approval_reply" => false,
        "send_sms_to_supplier_when_item_approval_reply"   => false,
        "enable_comments_on_items"                        => true,
        "enable_ratings_on_items"                         => true,
        "enable_promotions_on_items"                      => true,
    ],
    "orders" => [
        "send_email_to_users_when_order_state_changed" => false,
        "send_sms_to_users_when_order_state_changed"   => false,
        "order_can_be_cancelled_after_approved"        => false,
        "sellers_commission"                           => 0,
        "max_commissions_num_for_sellers"              => 5,
        "our_commission"                               => 0,
    ],
    "journey_plan" => [
        'journy_plan_distance_to_mark_visit' => 5,   // in meter
    ],
];