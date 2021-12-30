<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

// @todo add custom email sending service

add_filter( 'dt_message_methods', function($list){
    $list['email'] = [
        'key' => 'email',
        'label' => 'Email'
    ];
    return $list;
});
