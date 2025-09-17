<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/asset_manager.php';

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\AssetManager\Models\Asset;

// Get notification settings from the module configuration
$thirty_days_notice = Capsule::table('tbladdonmodules')->where('module', 'asset_manager')->where('setting', 'thirty_days_notice')->value('value');
$sixty_days_notice = Capsule::table('tbladdonmodules')->where('module', 'asset_manager')->where('setting', 'sixty_days_notice')->value('value');
$ninety_days_notice = Capsule::table('tbladdonmodules')->where('module', 'asset_manager')->where('setting', 'ninety_days_notice')->value('value');

$today = new DateTime();

$expiring_assets = Asset::whereNotNull('warranty_end_date')->get();

foreach ($expiring_assets as $asset) {
    $warranty_end_date = new DateTime($asset->warranty_end_date);
    $diff = $today->diff($warranty_end_date)->days;

    if ($diff <= 90 && $diff > 60 && $ninety_days_notice === 'on') {
        send_warranty_notification($asset, 90);
    } elseif ($diff <= 60 && $diff > 30 && $sixty_days_notice === 'on') {
        send_warranty_notification($asset, 60);
    } elseif ($diff <= 30 && $diff > 0 && $thirty_days_notice === 'on') {
        send_warranty_notification($asset, 30);
    }
}

function send_warranty_notification($asset, $days)
{
    // In a real application, you would send an email here.
    // This is just a placeholder.
    logActivity("Warranty for asset #{$asset->id} ({$asset->name}) is expiring in {$days} days.");
}
