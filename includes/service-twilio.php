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
return;


// https://www.twilio.com/docs/sms/quickstart/php
require __DIR__ . '/vendor/autoload.php';
use Twilio\Rest\Client;

function dt_send_bulk_twilio_message() {


// Your Account SID and Auth Token from twilio.com/console
    $account_sid = 'ACXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
    $auth_token = 'your_auth_token';
// In production, these should be environment variables. E.g.:
// $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]

// A Twilio number you own with SMS capabilities
    $twilio_number = "+15017122661";

    $client = new Client($account_sid, $auth_token);
    dt_write_log($client);


    $client->messages->create(
// Where to send a text message (your cell phone?)
        '+15558675310',
        array(
            'from' => $twilio_number,
            'body' => 'I sent this message in under 10 minutes!'
        )
    );
}

