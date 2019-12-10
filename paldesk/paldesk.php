<?php

if (!defined('WHMCS'))
    die('This file cannot be accessed directly');

function paldesk_config()
{
    $configarray = array(
        'name'          => 'Paldesk',
        'description'   => 'Brings Paldesk to your WHMCS installation to chat with your users.',
        'version'       => '1.0',
        'author'        => 'Paldesk',
        'fields'        => array(
            'apiKey' => array(
                'FriendlyName'  => 'apiKey',
                'Type'          => 'text',
                'Size'          => '64',
                'Description'   => 'Add your Paldesk Widget API Key',
                'Default'       => ''
            )
        )
    );

    return $configarray;
}

function paldesk_activate()
{
    return array(
        'status'        => 'success',
        'description'   => 'Paldesk module has been activated. Add your Widget API Key, you can find this by logging into Paldesk.'
    );
}

function paldesk_deactivate()
{
    return array(
        'status'        => 'success',
        'description'   => 'Paldesk module has been deactivated.'
    );
}

function paldesk_upgrade($vars)
{
    // No upgrade path yet.
}

function paldesk_output($vars)
{
    echo '
        <p>To configure, go to Setup -> Addon Modules -> Paldesk -> Configure.</p>

    ';
}

function paldesk_sidebar($vars)
{
    // n/a
}
