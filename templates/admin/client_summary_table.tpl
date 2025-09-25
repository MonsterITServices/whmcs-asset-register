<div class="clientssummarybox" id="assetManagerSummaryPanel">
    <div class="title">Assets</div>
    <form action="clientssummary.php" method="get" class="form-inline pull-right">
        <input type="hidden" name="userid" value="{$userid}">
        <div class="form-group">
            <label for="assets_per_page">Show:</label>
            <select name="assets_per_page" id="assets_per_page" class="form-control input-sm" onchange="this.form.submit()">
                <option value="10" {if $per_page == 10}selected{/if}>10</option>
                <option value="25" {if $per_page == 25}selected{/if}>25</option>
                <option value="50" {if $per_page == 50}selected{/if}>50</option>
                <option value="all" {if $per_page == 'all'}selected{/if}>All</option>
            </select>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Asset Name</th>
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
                        <td>{$asset->type->name}</td>
                        <td>{$asset.serial_number}</td>
                        <td>{$asset.status}</td>
                        <td class="text-right">
                            <a href="addonmodules.php?module=asset_manager&action=edit-asset&id={$asset.id}" class="btn btn-sm btn-default">Edit</a>
                        </td>
                    </tr>
                {foreachelse}
                    <tr>
                        <td colspan="6" class="text-center">No assets found for this client.</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    <div class="text-center">
        {$pagination nofilter}
    </div>
    <a href="addonmodules.php?module=asset_manager&action=add-asset&userid={$userid}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Add New Asset
    </a>
</div>