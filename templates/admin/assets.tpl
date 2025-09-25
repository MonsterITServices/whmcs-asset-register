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

<form action="" method="get" class="form-inline">
    <input type="hidden" name="module" value="asset_manager">
    <input type="hidden" name="action" value="list">

    <div class="form-group">
        <label for="filter_client_id">Client</label>
        <select name="filter_client_id" id="filter_client_id" class="form-control">
            <option value="">- All Clients -</option>
            {foreach from=$clients item=client}
                <option value="{$client->id}" {if $filter_client_id == $client->id}selected{/if}>{$client->firstname} {$client->lastname}</option>
            {/foreach}
        </select>
    </div>

    <div class="form-group">
        <label for="filter_contact_id">Contact</label>
        <select name="filter_contact_id" id="filter_contact_id" class="form-control">
            <option value="">- All Contacts -</option>
            {foreach from=$contacts item=contact}
                <option value="{$contact->id}" {if $filter_contact_id == $contact->id}selected{/if}>{$contact->firstname} {$contact->lastname}</option>
            {/foreach}
        </select>
    </div>

    <div class="form-group">
        <label for="per_page">Per Page</label>
        <select name="per_page" id="per_page" class="form-control">
            <option value="10" {if $per_page == 10}selected{/if}>10</option>
            <option value="25" {if $per_page == 25}selected{/if}>25</option>
            <option value="50" {if $per_page == 50}selected{/if}>50</option>
            <option value="100" {if $per_page == 100}selected{/if}>100</option>
            <option value="all" {if $per_page == 'all'}selected{/if}>All</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="addonmodules.php?module=asset_manager&action=list" class="btn btn-default">Clear</a>
</form>
<br>

{if $assets->count()}
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Asset Name</th>
                <th>Client Name</th>
                <th>Contact</th>
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
                    <td>
                        {if $asset.contact}
                            <a href="clientscontacts.php?userid={$asset.client.id}&contactid={$asset.contact.id}">{$asset.contact.firstname} {$asset.contact.lastname}</a>
                        {else}
                            N/A
                        {/if}
                    </td>
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

    <div class="text-center">
        {$pagination nofilter}
    </div>

{else}
    <div class="alert alert-info">
        No assets found.
    </div>
{/if}

        </div>
    </div>
</div>
