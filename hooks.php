<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

// Hook function for the Admin Area Client Summary Page
function asset_manager_admin_summary_hook($vars)
{
    try {
        $userId = (int)$vars['userid'];
        $perPage = isset($_REQUEST['assets_per_page']) ? $_REQUEST['assets_per_page'] : 10;
        $page = isset($_REQUEST['assets_page']) ? (int)$_REQUEST['assets_page'] : 1;
        $assetQuery = \WHMCS\Module\Addon\AssetManager\Models\Asset::where('userid', $userId)->with('type');
        $totalResults = $assetQuery->count();
        if ($perPage !== 'all') {
            $assetQuery->skip(($page - 1) * $perPage)->take($perPage);
        }
        $assets = $assetQuery->get();
        $pagination = '';
        if ($perPage !== 'all' && $totalResults > 0) {
            $totalPages = ceil($totalResults / $perPage);
            if ($totalPages > 1) {
                $pagination .= '<ul class="pagination">';
                if ($page > 1) {
                    $prevPage = $page - 1;
                    $pagination .= '<li><a href="clientssummary.php?userid=' . $userId . '&assets_page=' . $prevPage . '&assets_per_page=' . $perPage . '">&laquo;</a></li>';
                }
                for ($i = 1; $i <= $totalPages; $i++) {
                    $active = ($i == $page) ? 'class="active"' : '';
                    $pagination .= '<li ' . $active . '><a href="clientssummary.php?userid=' . $userId . '&assets_page=' . $i . '&assets_per_page=' . $perPage . '">' . $i . '</a></li>';
                }
                if ($page < $totalPages) {
                    $nextPage = $page + 1;
                    $pagination .= '<li><a href="clientssummary.php?userid=' . $userId . '&assets_page=' . $nextPage . '&assets_per_page=' . $perPage . '">&raquo;</a></li>';
                }
                $pagination .= '</ul>';
            }
        }
        $smarty = new \Smarty();
        $smarty->assign('assets', $assets);
        $smarty->assign('userid', $userId);
        $smarty->assign('per_page', $perPage);
        $smarty->assign('pagination', $pagination);
        $templatePath = __DIR__ . '/templates/admin/client_summary_table.tpl';
        if (file_exists($templatePath)) {
            return $smarty->fetch($templatePath);
        }
        return '<div class="errorbox">Asset Manager Error: client_summary_table.tpl not found.</div>';
    } catch (\Exception $e) {
        return '<div class="errorbox">Asset Manager Error: ' . $e->getMessage() . '</div>';
    }
}

// Hook function for the Admin Ticket Page
function asset_manager_ticket_link_hook($vars)
{
    $ticketId = $vars['ticketid'];
    $userId = $vars['userid'];
    if (isset($_POST['link_asset_to_ticket'])) {
        $assetId = (int)$_POST['asset_id'];
        if ($assetId > 0) {
            \WHMCS\Module\Addon\AssetManager\Models\TicketLink::updateOrCreate(['ticket_id' => $ticketId], ['asset_id' => $assetId]);
        } else {
            \WHMCS\Module\Addon\AssetManager\Models\TicketLink::where('ticket_id', $ticketId)->delete();
        }
    }
    $linkedAsset = \WHMCS\Module\Addon\AssetManager\Models\TicketLink::where('ticket_id', $ticketId)->first();
    $clientAssets = \WHMCS\Module\Addon\AssetManager\Models\Asset::where('userid', $userId)->get();
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
}

// Hook function for Client Area CSS
function asset_manager_client_css_hook($vars)
{
    if (isset($vars['modulename']) && $vars['modulename'] == 'asset_manager') {
        return '<link rel="stylesheet" href="modules/addons/asset_manager/assets/css/client.css">';
    }
}

// Hook function for Admin Area CSS and JS
function asset_manager_admin_head_hook($vars)
{
    $script = '<script type="text/javascript">
        $(document).ready(function() {
            var targetContainer = $(\'#clientsummarycontainer\');
            var assetPanel = $(\'#assetManagerSummaryPanel\');
            if (assetPanel.length && targetContainer.length) {
                assetPanel.appendTo(targetContainer);
            }
        });
    </script>';
    return '<link rel="stylesheet" href="modules/addons/asset_manager/assets/css/summary.css">' . $script;
}

// Hook function to add a menu item to the client area navbar
function asset_manager_navbar_hook($primaryNavbar)
{
    // The database is not available in this hook, so we cannot check the module setting.
    // The menu item will always be visible, but the linked pages are still protected.
    if ($primaryNavbar) {
        $primaryNavbar->addChild('My Assets', [
            'label' => 'My Assets',
            'uri' => 'index.php?m=asset_manager&action=assets',
            'order' => 70,
            'icon' => 'fa-hdd-o',
        ]);
    }
}

// Register all hooks
add_hook('AdminAreaClientSummaryPage', 1, 'asset_manager_admin_summary_hook');
add_hook('AdminAreaViewTicketPage', 300, 'asset_manager_ticket_link_hook');
add_hook('ClientAreaHeadOutput', 1, 'asset_manager_client_css_hook');
add_hook('AdminAreaHeadOutput', 1, 'asset_manager_admin_head_hook');
add_hook('ClientAreaPrimaryNavbar', 1, 'asset_manager_navbar_hook');