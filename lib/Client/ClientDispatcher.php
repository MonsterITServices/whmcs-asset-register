<?php
namespace WHMCS\Module\Addon\AssetManager\Client;

use WHMCS\Module\Addon\AssetManager\Models\Asset;
use WHMCS\Module\Addon\AssetManager\Models\AssetType;
use WHMCS\Authentication\CurrentUser;
use WHMCS\Database\Capsule;

class ClientDispatcher
{
    public function dispatch($action, $vars)
    {
        $action = str_replace(' ', '', ucwords(str_replace('-', ' ', $action)));
        $method = $action . 'Action';

        if (method_exists($this, $method)) {
            return $this->$method($vars);
        } else {
            return [
                'pagetitle' => 'Not Found',
                'templatefile' => 'error',
                'vars' => ['message' => 'The requested page was not found.'],
            ];
        }
    }

    public function assetsAction($vars)
    {
        $currentUser = new CurrentUser();
        $client = $currentUser->client();

        if (!$client) {
            return [
                'pagetitle' => 'My Assets',
                'templatefile' => 'asset_manager/templates/client/assets',
                'vars' => [
                    'assets' => collect(),
                    'error' => 'You must be logged in to view your assets.'
                ],
            ];
        }

        $settings = Capsule::table('tbladdonmodules')->where('module', 'asset_manager')->get()->pluck('value', 'setting')->all();
        $assets = Asset::where('userid', $client->id)->with('type')->get();

        return [
            'pagetitle' => 'My Assets',
            'breadcrumb' => ['index.php?m=asset_manager&action=assets' => 'My Assets'],
            'templatefile' => 'asset_manager/templates/client/assets',
            'vars' => [
                'assets' => $assets,
                'allow_add' => $settings['allow_client_add'] === 'on',
                'allow_delete' => $settings['allow_client_delete'] === 'on',
            ],
        ];
    }

    public function viewAssetAction($vars)
    {
        $currentUser = new CurrentUser();
        $client = $currentUser->client();
        $assetId = (int)$_REQUEST['id'];

        if (!$client) {
            return [ 'pagetitle' => 'Error', 'templatefile' => 'error', 'vars' => ['message' => 'Access Denied'], ];
        }

        $asset = Asset::where('id', $assetId)->where('userid', $client->id)->with('type', 'customFields.customField')->first();

        if (!$asset) {
            return [ 'pagetitle' => 'Asset Not Found', 'templatefile' => 'error', 'vars' => ['message' => 'The requested asset was not found.'], ];
        }

        return [
            'pagetitle' => $asset->name,
            'breadcrumb' => [
                'index.php?m=asset_manager&action=assets' => 'My Assets',
                'index.php?m=asset_manager&action=view-asset&id=' . $asset->id => $asset->name,
            ],
            'templatefile' => 'asset_manager/templates/client/asset_view',
            'vars' => [ 'asset' => $asset, ],
        ];
    }

    public function addAssetAction($vars)
    {
        $settings = Capsule::table('tbladdonmodules')->where('module', 'asset_manager')->get()->pluck('value', 'setting')->all();
        if ($settings['allow_client_add'] !== 'on') {
            return [ 'pagetitle' => 'Error', 'templatefile' => 'error', 'vars' => ['message' => 'Access Denied'], ];
        }

        $asset_types = AssetType::with('customFields')->get();

        return [
            'pagetitle' => 'Add New Asset',
            'breadcrumb' => [
                'index.php?m=asset_manager&action=assets' => 'My Assets',
                'index.php?m=asset_manager&action=add-asset' => 'Add Asset',
            ],
            'templatefile' => 'asset_manager/templates/client/add_asset',
            'vars' => [ 'asset_types' => $asset_types, ],
        ];
    }

    public function saveAssetAction($vars)
    {
        $settings = Capsule::table('tbladdonmodules')->where('module', 'asset_manager')->get()->pluck('value', 'setting')->all();
        if ($settings['allow_client_add'] !== 'on') {
            return [ 'pagetitle' => 'Error', 'templatefile' => 'error', 'vars' => ['message' => 'Access Denied'], ];
        }

        $currentUser = new CurrentUser();
        $client = $currentUser->client();

        if (!$client) { return; }

        $asset = new Asset();
        $asset->userid = $client->id;
        $asset->asset_type_id = $_POST['asset_type_id'];
        $asset->name = $_POST['name'];
        $asset->serial_number = $_POST['serial_number'];
        $asset->status = 'Active'; // Default status for client-added assets
        $asset->save();

        header('Location: index.php?m=asset_manager');
        exit;
    }

    public function deleteAssetAction($vars)
    {
        $settings = Capsule::table('tbladdonmodules')->where('module', 'asset_manager')->get()->pluck('value', 'setting')->all();
        if ($settings['allow_client_delete'] !== 'on') {
            return [ 'pagetitle' => 'Error', 'templatefile' => 'error', 'vars' => ['message' => 'Access Denied'], ];
        }

        $currentUser = new CurrentUser();
        $client = $currentUser->client();
        $assetId = (int)$_REQUEST['id'];

        if (!$client) { return; }

        $asset = Asset::where('id', $assetId)->where('userid', $client->id)->first();
        if ($asset) {
            $asset->delete();
        }

        header('Location: index.php?m=asset_manager');
        exit;
    }
}