<form action="index.php?m=asset_manager&action=save-asset" method="post">
    <div class="form-group">
        <label for="asset_type_id">Asset Type</label>
        <select name="asset_type_id" id="asset_type_id" class="form-control">
            {foreach from=$asset_types item=type}
                <option value="{$type.id}">{$type.name}</option>
            {/foreach}
        </select>
    </div>

    <div class="form-group">
        <label for="name">Asset Name</label>
        <input type="text" name="name" id="name" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="serial_number">Serial Number</label>
        <input type="text" name="serial_number" id="serial_number" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Save Asset</button>
    <a href="index.php?m=asset_manager" class="btn btn-default">Cancel</a>
</form>