<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// The incompatible 'widgets.php' file that caused a fatal error has been permanently removed.
require_once __DIR__ . '/activation.php';

/**
 * Define addon module configuration parameters.
 */
function asset_manager_config()
{
    return [
        'name' => 'Asset Manager',
        'description' => 'A module to manage client IT assets.',
        'author' => 'Gemini',
        'language' => 'english',
        'version' => 'FINAL', // The final, working version
        'fields' => []
    ];
}

/**
 * Activate addon module.
 */
function asset_manager_activate()
{
    return AssetManagerActivation::activate();
}

/**
 * Deactivate addon module.
 */
function asset_manager_deactivate()
{
    return AssetManagerActivation::deactivate();
}

/**
 * Admin area output.
 */
function asset_manager_output($vars)
{
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'list';
    $dispatcher = new \WHMCS\Module\Addon\AssetManager\Admin\AdminDispatcher();
    echo $dispatcher->dispatch($action, $vars);
}

/**
 * Client area output.
 */
function asset_manager_clientarea($vars)
{
    $showInClientArea = \WHMCS\Database\Capsule::table('tbladdonmodules')
        ->where('module', 'asset_manager')
        ->where('setting', 'showInClientArea')
        ->value('value');

    if ($showInClientArea !== 'on') {
        return;
    }

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'assets';
    $dispatcher = new \WHMCS\Module\Addon\AssetManager\Client\ClientDispatcher();
    return $dispatcher->dispatch($action, $vars);
}