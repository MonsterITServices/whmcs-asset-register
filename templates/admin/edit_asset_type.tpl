<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <h2>Edit Asset Type</h2>

            <form action="addonmodules.php?module=asset_manager&action=save-asset-type" method="post">
                <input type="hidden" name="id" value="{$asset_type.id}">
                <div class="form-group">
                    <label for="name">Asset Type Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{$asset_type.name}" required>
                </div>

                <button type="submit" class="btn btn-primary">Save Asset Type</button>
                <a href="addonmodules.php?module=asset_manager&action=asset-types" class="btn btn-default">Cancel</a>
            </form>
        </div>
        <div class="col-md-6">
            <h2>Custom Fields</h2>

            <form action="addonmodules.php?module=asset_manager&action=save-custom-field" method="post">
                <input type="hidden" name="asset_type_id" value="{$asset_type.id}">
                <div class="form-group">
                    <label for="field_name">Field Name</label>
                    <input type="text" name="field_name" id="field_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="field_type">Field Type</label>
                    <select name="field_type" id="field_type" class="form-control">
                        <option value="text">Text</option>
                        <option value="textarea">Text Area</option>
                        <option value="dropdown">Dropdown</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="field_options">Field Options (comma separated for dropdowns)</label>
                    <input type="text" name="field_options" id="field_options" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Add Custom Field</button>
            </form>

            <br><br>

            {if $custom_fields}
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Field Name</th>
                            <th>Field Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$custom_fields item=field}
                            <tr>
                                <td>{$field.field_name}</td>
                                <td>{$field.field_type}</td>
                                <td>
                                    <a href="addonmodules.php?module=asset_manager&action=delete-custom-field&id={$field.id}" class="btn btn-sm btn-danger">Delete</a>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            {else}
                <div class="alert alert-info">
                    No custom fields found for this asset type.
                </div>
            {/if}
        </div>
    </div>
</div>
