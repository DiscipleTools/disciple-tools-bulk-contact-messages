<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

// @todo add custom email sending service

add_filter( 'dt_message_methods', function($list){
    $list['twilio'] = [
        'key' => 'twilio',
        'label' => 'Text Message'
    ];
    return $list;
});
