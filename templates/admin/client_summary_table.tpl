<div class="clientssummarybox">
    <div class="title">Assets</div>
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
    <a href="addonmodules.php?module=asset_manager&action=add-asset&userid={$userid}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Add New Asset
    </a>
</div>