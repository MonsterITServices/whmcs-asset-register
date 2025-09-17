<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>Add New Asset Type</h2>

            <form action="addonmodules.php?module=asset_manager&action=save-asset-type" method="post">
                <div class="form-group">
                    <label for="name">Asset Type Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Save Asset Type</button>
                <a href="addonmodules.php?module=asset_manager&action=asset-types" class="btn btn-default">Cancel</a>
            </form>
        </div>
    </div>
</div>
