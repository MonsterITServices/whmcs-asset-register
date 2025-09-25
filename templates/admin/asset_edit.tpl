<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>Edit Asset</h2>

            <form action="addonmodules.php?module=asset_manager&action=save-asset" method="post">
                <input type="hidden" name="id" value="{$asset.id}">

                <div class="form-group">
                    <label for="user_id">Client</label>
                    <select name="user_id" id="user_id" class="form-control">
                        {foreach from=$clients item=client}
                            <option value="{$client.id}" {if $asset->userid == $client.id}selected{/if}>{$client.firstname} {$client.lastname} ({$client.email})</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group">
                    <label for="contact_id">Contact</label>
                    <select name="contact_id" id="contact_id" class="form-control">
                        <option value="">None</option>
                        {foreach from=$contacts item=contact}
                            <option value="{$contact.id}" {if $asset->contact_id == $contact.id}selected{/if}>{$contact.firstname} {$contact.lastname} ({$contact.email})</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group">
                    <label for="asset_type_id">Asset Type</label>
                    <select name="asset_type_id" id="asset_type_id" class="form-control">
                        {foreach from=$asset_types item=type}
                            <option value="{$type.id}" {if $asset->asset_type_id == $type.id}selected{/if} data-custom-fields='{$type->customFields|json_encode}'>{$type.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div id="custom-fields-container"></div>

                <div class="form-group">
                    <label for="name">Asset Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{$asset.name}" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control">{$asset.description}</textarea>
                </div>

                <div class="form-group">
                    <label for="serial_number">Serial Number</label>
                    <input type="text" name="serial_number" id="serial_number" class="form-control" value="{$asset.serial_number}">
                </div>

<div class="form-group">
    <label for="product_number">Product Number</label>
    <input type="text" name="product_number" id="product_number" class="form-control" value="{$asset.product_number}">
</div>

<div class="form-group">
    <label for="mac_address">MAC Address</label>
    <input type="text" name="mac_address" id="mac_address" class="form-control" value="{$asset.mac_address}">
</div>

<div class="form-group">
    <label for="ip_address_type">IP Address</label>
    <select name="ip_address_type" id="ip_address_type" class="form-control" onchange="toggleIpAddressField(this.value)">
        <option value="dynamic" {if $asset.ip_address_type == 'dynamic'}selected{/if}>Dynamic IP</option>
        <option value="static" {if $asset.ip_address_type == 'static'}selected{/if}>Static IP</option>
    </select>
</div>

<div id="static-ip-container" style="display: {if $asset.ip_address_type == 'static'}block{else}none{/if};">
    <div class="form-group">
        <label for="ip_address">IP Address</label>
        <input type="text" name="ip_address" id="ip_address" class="form-control" value="{$asset.ip_address}">
    </div>
    <div class="form-group">
        <label for="subnet_mask">Subnet Mask</label>
        <input type="text" name="subnet_mask" id="subnet_mask" class="form-control" value="{$asset.subnet_mask}">
    </div>
    <div class="form-group">
        <label for="gateway">Gateway</label>
        <input type="text" name="gateway" id="gateway" class="form-control" value="{$asset.gateway}">
    </div>
    <div class="form-group">
        <label for="dns1">DNS 1</label>
        <input type="text" name="dns1" id="dns1" class="form-control" value="{$asset.dns1}">
    </div>
    <div class="form-group">
        <label for="dns2">DNS 2</label>
        <input type="text" name="dns2" id="dns2" class="form-control" value="{$asset.dns2}">
    </div>
</div>

<div class="form-group">
    <label for="admin_notes">Admin Notes</label>
    <textarea name="admin_notes" id="admin_notes" class="form-control">{$asset.admin_notes}</textarea>
</div>

                <div class="form-group">
                    <label for="purchase_date">Purchase Date</label>
                    <input type="date" name="purchase_date" id="purchase_date" class="form-control" value="{$asset.purchase_date}">
                </div>

                <div class="form-group">
                    <label for="warranty_end_date">Warranty End Date</label>
                    <input type="date" name="warranty_end_date" id="warranty_end_date" class="form-control" value="{$asset.warranty_end_date}">
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="Active" {if $asset.status == 'Active'}selected{/if}>Active</option>
                        <option value="Inactive" {if $asset.status == 'Inactive'}selected{/if}>Inactive</option>
                        <option value="Archived" {if $asset.status == 'Archived'}selected{/if}>Archived</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="addonmodules.php?module=asset_manager" class="btn btn-default">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const assetTypeSelect = document.getElementById('asset_type_id');
        const customFieldsContainer = document.getElementById('custom-fields-container');
        const existingValues = {$asset->customFields->pluck('field_value', 'custom_field_id')|json_encode};

        function renderCustomFields() {
            const selectedOption = assetTypeSelect.options[assetTypeSelect.selectedIndex];
            const customFields = JSON.parse(selectedOption.dataset.customFields || '[]');
            
            customFieldsContainer.innerHTML = '';

            customFields.forEach(field => {
                const formGroup = document.createElement('div');
                formGroup.className = 'form-group';

                const label = document.createElement('label');
                label.htmlFor = 'custom_field_' + field.id;
                label.textContent = field.field_name;

                let input;
                if (field.field_type === 'textarea') {
                    input = document.createElement('textarea');
                } else if (field.field_type === 'dropdown') {
                    input = document.createElement('select');
                    const options = field.field_options.split(',');
                    options.forEach(optionText => {
                        const option = document.createElement('option');
                        option.value = optionText.trim();
                        option.textContent = optionText.trim();
                        input.appendChild(option);
                    });
                } else {
                    input = document.createElement('input');
                    input.type = 'text';
                }

                input.name = 'custom_fields[' + field.id + ']';
                input.id = 'custom_field_' + field.id;
                input.className = 'form-control';
                if (existingValues[field.id]) {
                    input.value = existingValues[field.id];
                }

                formGroup.appendChild(label);
                formGroup.appendChild(input);
                customFieldsContainer.appendChild(formGroup);
            });
        }

        assetTypeSelect.addEventListener('change', renderCustomFields);
        renderCustomFields();
    });
</script>
