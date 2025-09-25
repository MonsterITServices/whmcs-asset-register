{if $error}
    <div class="alert alert-danger">
        {$error}
    </div>
{/if}

{if $allow_add}
<a href="index.php?m=asset_manager&action=add-asset" class="btn btn-primary">
    <i class="fas fa-plus"></i> Add New Asset
</a>
<br /><br />
{/if}

{if $assets->count()}
    <form action="" method="get" class="form-inline">
        <input type="hidden" name="m" value="asset_manager">
        <input type="hidden" name="action" value="assets">
        <div class="form-group">
            <label for="per_page">Show:</label>
            <select name="per_page" id="per_page" class="form-control" onchange="this.form.submit()">
                <option value="10" {if $per_page == 10}selected{/if}>10</option>
                <option value="25" {if $per_page == 25}selected{/if}>25</option>
                <option value="50" {if $per_page == 50}selected{/if}>50</option>
                <option value="all" {if $per_page == 'all'}selected{/if}>All</option>
            </select>
        </div>
    </form>
    <br>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Asset Name</th>
                <th>Asset Type</th>
                <th>Serial Number</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$assets item=asset}
                <tr>
                    <td>{$asset.name}</td>
                    <td>{$asset->type->name}</td>
                    <td>{$asset.serial_number}</td>
                    <td>{$asset.status}</td>
                    <td class="text-right">
                        <a href="index.php?m=asset_manager&action=view-asset&id={$asset.id}" class="btn btn-sm btn-default">View Details</a>
                        {if $allow_delete}
                        <a href="index.php?m=asset_manager&action=delete-asset&id={$asset.id}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this asset?')">Delete</a>
                        {/if}
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