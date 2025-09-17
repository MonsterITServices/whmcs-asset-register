<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>Asset Types</h2>
            <p>Manage the types of assets you want to track.</p>

            <a href="addonmodules.php?module=asset_manager&action=add-asset-type" class="btn btn-primary">Add New Asset Type</a>

            <br /><br />

            {if $asset_types}
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$asset_types item=type}
                            <tr>
                                <td>{$type.id}</td>
                                <td>{$type.name}</td>
                                <td>
                                    <a href="addonmodules.php?module=asset_manager&action=edit-asset-type&id={$type.id}" class="btn btn-sm btn-default">Edit</a>
                                    <a href="addonmodules.php?module=asset_manager&action=delete-asset-type&id={$type.id}" class="btn btn-sm btn-danger">Delete</a>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            {else}
                <div class="alert alert-info">
                    No asset types found.
                </div>
            {/if}
        </div>
    </div>
</div>
