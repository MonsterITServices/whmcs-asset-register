<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>Import Assets from CSV</h2>

            {if $error}
                <div class="alert alert-danger">
                    <strong>Error:</strong> {$error}
                </div>
            {/if}

            {if $success_count}
                <div class="alert alert-success">
                    Successfully imported {$success_count} assets.
                </div>
            {/if}

            <form action="addonmodules.php?module=asset_manager&action=do-import" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="csv_file">CSV File</label>
                    <input type="file" name="csv_file" id="csv_file" required>
                    <p class="help-block">Select a CSV file to import. The required columns are: Client Email, Asset Name, Asset Type, Serial Number, Status.</p>
                </div>

                <button type="submit" class="btn btn-primary">Import</button>
                <a href="addonmodules.php?module=asset_manager" class="btn btn-default">Cancel</a>
            </form>
        </div>
    </div>
</div>
