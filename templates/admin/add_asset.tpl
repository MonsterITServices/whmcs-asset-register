<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>Add New Asset</h2>

            <form action="addonmodules.php?module=asset_manager&action=save-asset" method="post">
                <div class="form-group">
                    <label for="user_id">Client</label>
                    <select name="user_id" id="user_id" class="form-control">
                        {foreach from=$clients item=client}
                            <option value="{$client.id}">{$client.firstname} {$client.lastname} ({$client.email})</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group">
                    <label for="contact_id">Contact</label>
                    <select name="contact_id" id="contact_id" class="form-control">
                        <option value="">None</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="asset_type_id">Asset Type</label>
                    <select name="asset_type_id" id="asset_type_id" class="form-control">
                        {foreach from=$asset_types item=type}
                            <option value="{$type.id}" data-custom-fields='{$type->customFields|json_encode}'>{$type.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div id="custom-fields-container"></div>

                <div class="form-group">
                    <label for="name">Asset Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label for="serial_number">Serial Number</label>
                    <input type="text" name="serial_number" id="serial_number" class="form-control">
                </div>
				
				<div class="form-group">
					<label for="product_number">Product Number</label>
					<input type="text" name="product_number" id="product_number" class="form-control">
				</div>

				<div class="form-group">
					<label for="mac_address">MAC Address</label>
					<input type="text" name="mac_address" id="mac_address" class="form-control">
				</div>

				<div class="form-group">
					<label for="ip_address_type">IP Address</label>
					<select name="ip_address_type" id="ip_address_type" class="form-control" onchange="toggleIpAddressField(this.value)">
						<option value="dynamic">Dynamic IP</option>
						<option value="static">Static IP</option>
					</select>
				</div>

<div id="static-ip-container" style="display: none;">
    <div class="form-group">
        <label for="ip_address">IP Address</label>
        <input type="text" name="ip_address" id="ip_address" class="form-control">
    </div>
    <div class="form-group">
        <label for="subnet_mask">Subnet Mask</label>
        <input type="text" name="subnet_mask" id="subnet_mask" class="form-control">
    </div>
    <div class="form-group">
        <label for="gateway">Gateway</label>
        <input type="text" name="gateway" id="gateway" class="form-control">
    </div>
    <div class="form-group">
        <label for="dns1">DNS 1</label>
        <input type="text" name="dns1" id="dns1" class="form-control">
    </div>
    <div class="form-group">
        <label for="dns2">DNS 2</label>
        <input type="text" name="dns2" id="dns2" class="form-control">
    </div>
</div>

				<div class="form-group">
					<label for="admin_notes">Admin Notes</label>
					<textarea name="admin_notes" id="admin_notes" class="form-control"></textarea>
				</div>
				
                <div class="form-group">
                    <label for="purchase_date">Purchase Date</label>
                    <input type="date" name="purchase_date" id="purchase_date" class="form-control">
                </div>

                <div class="form-group">
                    <label for="warranty_end_date">Warranty End Date</label>
                    <input type="date" name="warranty_end_date" id="warranty_end_date" class="form-control">
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Archived">Archived</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Save Asset</button>
                <a href="addonmodules.php?module=asset_manager" class="btn btn-default">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const assetTypeSelect = document.getElementById('asset_type_id');
        const customFieldsContainer = document.getElementById('custom-fields-container');

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

                formGroup.appendChild(label);
                formGroup.appendChild(input);
                customFieldsContainer.appendChild(formGroup);
            });
        }

        assetTypeSelect.addEventListener('change', renderCustomFields);
        renderCustomFields();

        const clientId = document.getElementById('user_id');
        const contactSelect = document.getElementById('contact_id');
        const contacts = {$contacts|json_encode};

        function populateContacts() {
            const selectedClientId = clientId.value;
            contactSelect.innerHTML = '<option value="">None</option>';

            contacts.forEach(contact => {
                if (contact.userid == selectedClientId) {
                    const option = document.createElement('option');
                    option.value = contact.id;
                    option.textContent = contact.firstname + ' ' + contact.lastname + ' (' + contact.email + ')';
                    contactSelect.appendChild(option);
                }
            });
        }

        clientId.addEventListener('change', populateContacts);
        populateContacts(); // Initial population
    });
</script>
