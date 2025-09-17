<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>Asset Manager Settings</h2>

            {if $success}
                <div class="alert alert-success">Settings saved successfully.</div>
            {/if}

            <form action="addonmodules.php?module=asset_manager&action=save-settings" method="post">
                <input type="hidden" name="token" value="{$token}" />

                <h4>Client Area Settings</h4>
                <div class="form-group">
                    <label for="showInClientArea">Show in Client Area</label>
                    <input type="checkbox" name="showInClientArea" id="showInClientArea" {if $settings.showInClientArea == 'on'}checked{/if}>
                    <span class="help-block">Tick to show the asset list in the client area.</span>
                </div>
                <div class="form-group">
                    <label for="allow_client_add">Allow Clients to Add Assets</label>
                    <input type="checkbox" name="allow_client_add" id="allow_client_add" {if $settings.allow_client_add == 'on'}checked{/if}>
                    <span class="help-block">Tick to allow clients to add new assets from the client area.</span>
                </div>
                <div class="form-group">
                    <label for="allow_client_delete">Allow Clients to Delete Assets</label>
                    <input type="checkbox" name="allow_client_delete" id="allow_client_delete" {if $settings.allow_client_delete == 'on'}checked{/if}>
                    <span class="help-block">Tick to allow clients to delete their own assets.</span>
                </div>

                <hr>

                <h4>Warranty Notifications</h4>
                <div class="form-group">
                    <label for="thirty_days_notice">30 Day Warranty Notice</label>
                    <input type="checkbox" name="thirty_days_notice" id="thirty_days_notice" {if $settings.thirty_days_notice == 'on'}checked{/if}>
                    <span class="help-block">Tick to send a notification 30 days before warranty expiration.</span>
                </div>
                <div class="form-group">
                    <label for="sixty_days_notice">60 Day Warranty Notice</label>
                    <input type="checkbox" name="sixty_days_notice" id="sixty_days_notice" {if $settings.sixty_days_notice == 'on'}checked{/if}>
                    <span class="help-block">Tick to send a notification 60 days before warranty expiration.</span>
                </div>
                <div class="form-group">
                    <label for="ninety_days_notice">90 Day Warranty Notice</label>
                    <input type="checkbox" name="ninety_days_notice" id="ninety_days_notice" {if $settings.ninety_days_notice == 'on'}checked{/if}>
                    <span class="help-block">Tick to send a notification 90 days before warranty expiration.</span>
                </div>

                <hr>

                <h4>Module Data</h4>
                <div class="form-group">
                    <label for="delete_on_deactivate" style="color: red;">Delete Data on Deactivation</label>
                    <input type="checkbox" name="delete_on_deactivate" id="delete_on_deactivate" {if $settings.delete_on_deactivate == 'on'}checked{/if}>
                    <span class="help-block">
                        <strong>Warning:</strong> If this box is ticked, all asset manager tables and data will be permanently deleted when the module is deactivated.
                    </span>
                </div>

                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
        </div>
    </div>
</div>