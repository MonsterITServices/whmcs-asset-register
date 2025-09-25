<?php
namespace WHMCS\Module\Addon\AssetManager\Admin;

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\AssetManager\Helpers\NotificationHelper;
use WHMCS\Module\Addon\AssetManager\Helpers\ExportHelper;
use WHMCS\Module\Addon\AssetManager\Helpers\ImportHelper;
use WHMCS\Module\Addon\AssetManager\Models\Asset;
use WHMCS\Module\Addon\AssetManager\Models\AssetType;
use WHMCS\Module\Addon\AssetManager\Models\CustomField;
use WHMCS\Module\Addon\AssetManager\Models\CustomFieldValue;
use WHMCS\Module\Addon\AssetManager\Models\TicketLink;

class AdminDispatcher
{
    public function dispatch($action, $vars)
    {
		if ($action === 'list') {
			$action = 'assets';
		}
        if ($action === 'export') {
            return ExportHelper::exportToCsv();
        }
        if ($action === 'import') {
            return $this->renderWithMenu($this->displayImportPage());
        }
        if ($action === 'do-import') {
            return $this->doImport();
        }
        if ($action === 'save-asset') {
            return $this->saveAsset();
        }
        if ($action === 'delete-asset') {
            return $this->deleteAsset();
        }
        if ($action === 'save-asset-type') {
            return $this->saveAssetType();
        }
        if ($action === 'save-custom-field') {
            return $this->saveCustomField();
        }
        if ($action === 'delete-custom-field') {
            return $this->deleteCustomField();
        }
        if ($action === 'save-settings') {
            return $this->saveSettings();
        }

        $action = str_replace(' ', '', ucwords(str_replace('-', ' ', $action)));
        $method = 'display' . $action . 'Page';

        if (method_exists($this, $method)) {
            return $this->renderWithMenu($this->$method($vars));
        } else {
            return $this->renderWithMenu(NotificationHelper::error('Invalid action requested.'));
        }
    }

    protected function displayAssetsPage($vars)
    {
        // Get filter and pagination parameters
        $filterClientId = isset($_REQUEST['filter_client_id']) ? (int)$_REQUEST['filter_client_id'] : null;
        $filterContactId = isset($_REQUEST['filter_contact_id']) ? (int)$_REQUEST['filter_contact_id'] : null;
        $perPage = isset($_REQUEST['per_page']) ? $_REQUEST['per_page'] : 10;
        $page = isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1;

        // Start building the asset query
        $assetQuery = Asset::with('client', 'type');

        // Apply filters
        if ($filterClientId) {
            $assetQuery->where('userid', $filterClientId);
        }
        if ($filterContactId) {
            $assetQuery->where('contact_id', $filterContactId);
        }

        // Get total count for pagination
        $totalResults = $assetQuery->count();

        // Apply manual pagination
        if ($perPage !== 'all') {
            $assetQuery->skip(($page - 1) * $perPage)->take($perPage);
        }

        $assets = $assetQuery->get();
        
        // Manually fetch and attach contact information
        $contactIds = $assets->pluck('contact_id')->filter()->unique()->toArray();
        if (!empty($contactIds)) {
            $contactsData = Capsule::table('tblcontacts')->whereIn('id', $contactIds)->get()->keyBy('id');
            $assets->each(function ($asset) use ($contactsData) {
                if (isset($contactsData[$asset->contact_id])) {
                    $asset->contact = (array) $contactsData[$asset->contact_id];
                } else {
                    $asset->contact = null;
                }
            });
        } else {
            $assets->each(function ($asset) {
                $asset->contact = null;
            });
        }

        // Manually generate pagination links
        $pagination = '';
        if ($perPage !== 'all' && $totalResults > 0) {
            $totalPages = ceil($totalResults / $perPage);
            if ($totalPages > 1) {
                $pagination .= '<ul class="pagination">';
                if ($page > 1) {
                    $prevPage = $page - 1;
                    $pagination .= '<li><a href="addonmodules.php?module=asset_manager&action=list&page=' . $prevPage . '&per_page=' . $perPage . '&filter_client_id=' . $filterClientId . '&filter_contact_id=' . $filterContactId . '">&laquo;</a></li>';
                }
                for ($i = 1; $i <= $totalPages; $i++) {
                    $active = ($i == $page) ? 'class="active"' : '';
                    $pagination .= '<li ' . $active . '><a href="addonmodules.php?module=asset_manager&action=list&page=' . $i . '&per_page=' . $perPage . '&filter_client_id=' . $filterClientId . '&filter_contact_id=' . $filterContactId . '">' . $i . '</a></li>';
                }
                if ($page < $totalPages) {
                    $nextPage = $page + 1;
                    $pagination .= '<li><a href="addonmodules.php?module=asset_manager&action=list&page=' . $nextPage . '&per_page=' . $perPage . '&filter_client_id=' . $filterClientId . '&filter_contact_id=' . $filterContactId . '">&raquo;</a></li>';
                }
                $pagination .= '</ul>';
            }
        }

        // Get data for filter dropdowns
        $clients = Capsule::table('tblclients')->select('id', 'firstname', 'lastname')->get();
        $contacts = Capsule::table('tblcontacts')->select('id', 'firstname', 'lastname')->get();

        return $this->render('assets', [
            'assets' => $assets,
            'clients' => $clients,
            'contacts' => $contacts,
            'filter_client_id' => $filterClientId,
            'filter_contact_id' => $filterContactId,
            'per_page' => $perPage,
            'pagination' => $pagination,
        ]);
    }

    protected function displayImportPage($vars = [])
    {
        return $this->render('import', $vars);
    }

    protected function doImport()
    {
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == UPLOAD_ERR_OK) {
            try {
                $successCount = ImportHelper::importFromCsv($_FILES['csv_file']['tmp_name']);
                return $this->renderWithMenu($this->displayImportPage(['success_count' => $successCount]));
            } catch (\Exception $e) {
                return $this->renderWithMenu($this->displayImportPage(['error' => $e->getMessage()]));
            }
        } else {
            return $this->renderWithMenu($this->displayImportPage(['error' => 'File upload failed.']));
        }
    }

	protected function displayAddAssetPage($vars)
	{
		$clientsData = Capsule::table('tblclients')->where('status', 'Active')->get();
		$clients = [];
		foreach ($clientsData as $client) {
			$clients[] = (array)$client;
		}

        $contactsData = Capsule::table('tblcontacts')->get();
        $contacts = [];
        foreach ($contactsData as $contact) {
            $contacts[] = (array)$contact;
        }

		$asset_types = AssetType::with('customFields')->get();
		return $this->render('add_asset', [
			'clients' => $clients,
            'contacts' => $contacts,
			'asset_types' => $asset_types,
		]);
	}

    protected function displayEditAssetPage($vars)
	{
		        $asset = Asset::with('customFields.customField')->find($_REQUEST['id']);
		$clientsData = Capsule::table('tblclients')->where('status', 'Active')->get();
		$clients = [];
		foreach ($clientsData as $client) {
			$clients[] = (array)$client;
		}

        $contactsData = Capsule::table('tblcontacts')->where('userid', $asset->userid)->get();
        $contacts = [];
        foreach ($contactsData as $contact) {
            $contacts[] = (array)$contact;
        }

		$asset_types = AssetType::with('customFields')->get();
		return $this->render('asset_edit', [
			'asset' => $asset,
			'clients' => $clients,
            'contacts' => $contacts,
			'asset_types' => $asset_types,
		]);
	}

    protected function saveAsset()
    {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $asset = Asset::find($_POST['id']);
        } else {
            $asset = new Asset();
        }

        $asset->userid = $_POST['user_id'];
        $asset->contact_id = $_POST['contact_id'];
        $asset->asset_type_id = $_POST['asset_type_id'];
        $asset->name = $_POST['name'];
        $asset->description = $_POST['description'];
        $asset->serial_number = $_POST['serial_number'];
		    $asset->product_number = $_POST['product_number'];
    $asset->mac_address = $_POST['mac_address'];
$asset->ip_address_type = $_POST['ip_address_type'];
if ($_POST['ip_address_type'] === 'static') {
    $asset->ip_address = $_POST['ip_address'];
    $asset->subnet_mask = $_POST['subnet_mask'];
    $asset->gateway = $_POST['gateway'];
    $asset->dns1 = $_POST['dns1'];
    $asset->dns2 = $_POST['dns2'];
} else {
    $asset->ip_address = null;
    $asset->subnet_mask = null;
    $asset->gateway = null;
    $asset->dns1 = null;
    $asset->dns2 = null;
}
$asset->admin_notes = $_POST['admin_notes'];
        $asset->purchase_date = !empty($_POST['purchase_date']) ? $_POST['purchase_date'] : null;
        $asset->warranty_end_date = !empty($_POST['warranty_end_date']) ? $_POST['warranty_end_date'] : null;
        $asset->status = $_POST['status'];
        $asset->save();

        if (isset($_POST['custom_fields'])) {
            foreach ($_POST['custom_fields'] as $field_id => $field_value) {
                CustomFieldValue::updateOrCreate(
                    ['asset_id' => $asset->id, 'custom_field_id' => $field_id],
                    ['field_value' => $field_value]
                );
            }
        }

        header('Location: ' . $this->getModuleUrl('list'));
        exit;
    }

    protected function deleteAsset()
    {
        $asset = Asset::find($_REQUEST['id']);
        if ($asset) {
            $asset->delete();
            CustomFieldValue::where('asset_id', $_REQUEST['id'])->delete();
            TicketLink::where('asset_id', $_REQUEST['id'])->delete();
        }
        header('Location: ' . $this->getModuleUrl('list'));
        exit;
    }

    protected function displayAssetTypesPage($vars)
    {
        $asset_types = AssetType::all();
        return $this->render('asset_types', ['asset_types' => $asset_types]);
    }

    protected function displayAddAssetTypePage($vars)
    {
        return $this->render('add_asset_type', []);
    }

    protected function saveAssetType()
    {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $assetType = AssetType::find($_POST['id']);
        } else {
            $assetType = new AssetType();
        }
        $assetType->name = $_POST['name'];
        $assetType->save();
        header('Location: ' . $this->getModuleUrl('asset-types'));
        exit;
    }

    protected function displayEditAssetTypePage($vars)
    {
        $asset_type = AssetType::with('customFields')->find($_REQUEST['id']);
        return $this->render('edit_asset_type', ['asset_type' => $asset_type]);
    }

    protected function saveCustomField()
    {
        $customField = new CustomField();
        $customField->asset_type_id = $_POST['asset_type_id'];
        $customField->field_name = $_POST['field_name'];
        $customField->field_type = $_POST['field_type'];
        $customField->field_options = $_POST['field_options'];
        $customField->save();
        header('Location: ' . $this->getModuleUrl('edit-asset-type&id=' . $_POST['asset_type_id']));
        exit;
    }

    protected function deleteCustomField()
    {
        $customField = CustomField::find($_REQUEST['id']);
        $asset_type_id = $customField->asset_type_id;
        $customField->delete();
        CustomFieldValue::where('custom_field_id', $_REQUEST['id'])->delete();
        header('Location: ' . $this->getModuleUrl('edit-asset-type&id=' . $asset_type_id));
        exit;
    }

    protected function displaySettingsPage($vars)
    {
        $settings = Capsule::table('tbladdonmodules')->where('module', 'asset_manager')->get()->pluck('value', 'setting')->all();
        return $this->render('settings', ['settings' => $settings, 'success' => $_GET['success'] ?? false, 'token' => $vars['_token']]);
    }

    protected function saveSettings()
{
    // Define all possible settings checkboxes
    $settings = [
        'showInClientArea',
        'allow_client_add',
        'allow_client_delete',
        'thirty_days_notice',
        'sixty_days_notice',
        'ninety_days_notice',
        'delete_on_deactivate'
    ];

    // Loop through and save the value for each setting
    foreach ($settings as $setting) {
        // If the checkbox is ticked, the value is 'on', otherwise it's 'off'
        $value = isset($_POST[$setting]) ? 'on' : 'off';

        Capsule::table('tbladdonmodules')->updateOrInsert(
            // Conditions to find the row
            ['module' => 'asset_manager', 'setting' => $setting],
            // Value to insert or update
            ['value' => $value]
        );
    }

    // Redirect back to the settings page with a success message
    header('Location: ' . $this->getModuleUrl('settings&success=true'));
    exit;
}

    protected function getModuleUrl($action)
    {
        return 'addonmodules.php?module=asset_manager&action=' . $action;
    }

	protected function render($template, $data)
	{
		$templatePath = __DIR__ . '/../../templates/admin/' . $template . '.tpl';
		if (!file_exists($templatePath)) {
        return NotificationHelper::error("Template file '{$template}.tpl' not found.");
    }

    try {
        $smarty = new \Smarty();
        foreach ($data as $key => $value) {
            $smarty->assign($key, $value);
        }
        return $smarty->fetch($templatePath);
    } catch (\Exception $e) {
        return "Smarty Error: " . $e->getMessage();
    }
}
    protected function renderWithMenu($content)
    {
        $action = $_REQUEST['action'] ?? 'list';
        $menu_items = [
            'list' => 'Assets',
            'asset-types' => 'Asset Types',
            'settings' => 'Settings'
        ];
        $menu = '<ul class="nav nav-tabs admin-tabs"> ';
        foreach ($menu_items as $item_action => $label) {
            $active = ($action == $item_action || in_array($action, ['add-asset', 'edit-asset', 'import', 'do-import']) && $item_action == 'list') || (in_array($action, ['add-asset-type', 'edit-asset-type']) && $item_action == 'asset-types') ? 'active' : '';
            $menu .= '<li class="' . $active . '"><a href="addonmodules.php?module=asset_manager&action=' . $item_action . '">' . $label . '</a></li>';
        }
        $menu .= '</ul>';
        return $menu . '<div class="tab-content admin-tabs">' . $content . '</div>';
    }
}
