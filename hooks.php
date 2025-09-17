<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\AssetManager\Models\Asset;
use WHMCS\Module\Addon\AssetManager\Models\TicketLink;

/**
 * This hook adds the asset table directly to the client's summary page in the admin area.
 */
add_hook('AdminAreaClientSummaryPage', 99, function($vars) {
    try {
        $userId = (int)$vars['userid'];
        $assets = Asset::where('userid', $userId)->with('type')->get();

        $smarty = new \Smarty();
        $smarty->assign('assets', $assets);
        $smarty->assign('userid', $userId);

        $templatePath = __DIR__ . '/templates/admin/client_summary_table.tpl';
        if (file_exists($templatePath)) {
            return $smarty->fetch($templatePath);
        }
        return '<div class="errorbox">Asset Manager Error: client_summary_table.tpl not found.</div>';

    } catch (\Exception $e) {
        return '<div class="errorbox">Asset Manager Error: ' . $e->getMessage() . '</div>';
    }
});

/**
 * This hook adds the "My Assets" panel to the client area dashboard.
 */
add_hook('ClientAreaHomepagePanels', 1, function ($vars) {
    if (!isset($vars['panels']) || is_null($vars['panels'])) {
        return;
    }

    $showInClientArea = Capsule::table('tbladdonmodules')
        ->where('module', 'asset_manager')
        ->where('setting', 'showInClientArea')
        ->value('value');

    if ($showInClientArea !== 'on') {
        return;
    }

    $currentUser = new WHMCS\Authentication\CurrentUser();
    $client = $currentUser->client();
    if (!$client) {
        return;
    }

    $assetCount = Asset::where('userid', $client->id)->count();

    $newPanel = $vars['panels']->addChild('my-assets-panel', [
        'name' => 'My Assets',
        'label' => 'My Assets',
        'icon' => 'fa-hdd-o',
        'order' => 100,
        'bodyHtml' => '<p>You have ' . $assetCount . ' registered asset(s).</p>',
        'footerHtml' => '<a href="index.php?m=asset_manager" class="btn btn-default btn-sm"><i class="fas fa-desktop"></i> Manage Assets</a>'
    ]);
});

/**
 * This hook adds the asset linking feature to the ticket view page.
 */
add_hook('AdminAreaViewTicketPage', 300, function($vars) {
    $ticketId = $vars['ticketid'];
    $userId = $vars['userid'];
    if (isset($_POST['link_asset_to_ticket'])) {
        $assetId = (int)$_POST['asset_id'];
        if ($assetId > 0) {
            TicketLink::updateOrCreate(['ticket_id' => $ticketId], ['asset_id' => $assetId]);
        } else {
            TicketLink::where('ticket_id', $ticketId)->delete();
        }
    }
    $linkedAsset = TicketLink::where('ticket_id', $ticketId)->first();
    $clientAssets = Asset::where('userid', $userId)->get();
    $options = '<option value="0">-- None --</option>';
    foreach ($clientAssets as $asset) {
        $selected = ($linkedAsset && $linkedAsset->asset_id == $asset->id) ? 'selected' : '';
        $options .= "<option value=\"{$asset->id}\" {$selected}>{$asset->name} ({$asset->serial_number})</option>";
    }
    return '
        <div class="ticket-asset-linking"><form method="post" action=""><div class="row"><div class="col-sm-12">
        <label for="asset_id">Linked Asset</label>
        <select name="asset_id" id="asset_id" class="form-control">' . $options . '</select>
        <button type="submit" name="link_asset_to_ticket" class="btn btn-primary btn-sm" style="margin-top:10px;">Link Asset</button>
        </div></div></form></div>';
});
/**
 * This hook adds the client area CSS to the page header.
 */
add_hook('ClientAreaHeadOutput', 1, function($vars) {
    // Check if the current page is part of the asset_manager module
    if (isset($vars['modulename']) && $vars['modulename'] == 'asset_manager') {
        return '<link rel="stylesheet" href="modules/addons/asset_manager/assets/css/client.css">';
    }
});