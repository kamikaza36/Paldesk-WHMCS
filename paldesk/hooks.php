<?php

if (!defined('WHMCS'))
    die('This file cannot be accessed directly');

use Illuminate\Database\Capsule\Manager as Capsule;

add_hook("ClientAreaFooterOutput", 1, function($vars) {

    $apikey = '';

    $data = Capsule::table('tbladdonmodules')
        ->select('value AS apiKey')
        ->where('setting', '=', 'apiKey')
        ->where('module', '=', 'Paldesk')
        ->first();

    if ($data) {
        $apiKey = $data->apiKey;
    }

    $params = array();
    $userData = '';

    if (isset($vars['clientsdetails']) && $vars['clientsdetails']['status'] == 'Active') {
        // Collecting user data
        $keys = array(
            'email'          => 'email',
            'companyname'    => 'company_name',
            'status'         => 'account_status'
        );

        // Manually construct data that is shown in Paldesk (firstname,lastname,email).
        $params[] = [
            'key' => 'externalId',
            'value' => $vars['clientsdetails']['id']
        ];

        $params[] = [
            'key'   => 'firstname',
            'value' => $vars['clientsdetails']['firstname']
        ];

        $params[] = [
            'key' => 'lastname',
            'value' => $vars['clientsdetails']['lastname']
        ];

        // Grab the date user was registered.
        $params[] = [
            'key' => 'created_at',
            'value' => strtotime($vars['client']['attributes']['datecreated'] . '00:00:00')
        ];

        // Has this member opt out of email communications?
        /*$params[] = [
            'key' => 'additionalFields:{ com_via_email }',
            'value' => $vars['clientsdetails']['email'] ? '1' : '0' . ' ' . $vars['clientsstats']['income']->toFull() . ' ' . $vars['clientdetails']['company_name'] . ' ' . $vars['clientdetails']['account_status'] . ' ' . strtotime($vars['client']['attributes']['datecreated'] . '00:00:00')
        ];
        */
        foreach ($keys as $key => $value) {
            if (isset($vars['clientsdetails'][$key])) {
                $params[] = array(
                    'key'   => $value,
                    'value' => html_entity_decode($vars['clientsdetails'][$key])
                );
            }
        }
        
        // Grab the clients income
        if (isset($vars['clientsstats']) && method_exists($vars['clientsstats']['income'], 'toFull')) {
            $params[] = array(
                'key' => 'income',
                'value' => $vars['clientsstats']['income']->toFull()
            );
        }

        if ($params) {
            $userDataArray = array();

            foreach ($params as $param) {
                $key = $param['key'];
                $value = $param['value'];
                $userDataArray[] = "{$key}: '{$value}'";
            }

            $userData = implode(",\n    ", $userDataArray);

        }
    }


    $output = "
        <script>\n
        custom_user_data = {\n
                apiKey: \"{$apiKey}\",
                " . ($userData) . "
            };\n
        </script>\n
        <script> \n
        if(\"undefined\"!==typeof requirejs)window.onload=function(e){requirejs([\"https://paldesk.io/api/widget-client?apiKey=\" + custom_user_data.apiKey],function(e){\"undefined\"!==typeof custom_user_data&&(beebeeate_config.user_data=custom_user_data),BeeBeeate.widget.new(beebeeate_config)})};
        else{var s=document.createElement(\"script\");s.async=!0,s.src=\"https://paldesk.io/api/widget-client?apiKey=\" + custom_user_data.apiKey,s.onload=function(){\"undefined\"!==typeof custom_user_data&&(beebeeate_config.user_data=custom_user_data),BeeBeeate.widget.new(beebeeate_config)};
        if(document.body){
            document.body.appendChild(s)
        }
        else if(document.head){
            document.head.appendChild(s)
        }
        }
    </script>\n
    ";

    return $output;
});
