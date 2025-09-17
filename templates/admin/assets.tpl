<link rel="stylesheet" type="text/css" href="modules/addons/asset_manager/assets/css/admin.css" />
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>Asset Manager</h2>
            <p>Manage your client's IT assets.</p>

            <div class="btn-group" role="group">
    <a href="addonmodules.php?module=asset_manager&action=add-asset" class="btn btn-primary">Add New Asset</a>
    <a href="addonmodules.php?module=asset_manager&action=import" class="btn btn-default">Import from CSV</a>
    <a href="addonmodules.php?module=asset_manager&action=export" class="btn btn-default">Export to CSV</a>
</div>

<br /><br />

{if $assets->count()}
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Asset Name</th>
                <th>Client Name</th>
                <th>Asset Type</th>
                <th>Serial Number</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$assets item=asset}
                <tr>
                    <td>{$asset.id}</td>
                    <td>{$asset.name}</td>
                    <td><a href="clientssummary.php?userid={$asset.client.id}">{$asset.client.firstname} {$asset.client.lastname}</a></td>
                    <td>{$asset->type->name}</td>
                    <td>{$asset.serial_number}</td>
                    <td>{$asset.status}</td>
                    <td class="text-right">
                        <a href="addonmodules.php?module=asset_manager&action=edit-asset&id={$asset.id}" class="btn btn-sm btn-default">Edit</a>
                        <a href="addonmodules.php?module=asset_manager&action=delete-asset&id={$asset.id}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this asset?')">Delete</a>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <div class="alert alert-info">
        No assets found.
    </div>
{/if}

        </div>
    </div>
</div>
