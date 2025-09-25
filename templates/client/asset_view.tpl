<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{$asset.name}</h3>
    </div>
    <div class="panel-body">
        <p><strong>Asset Type:</strong> {$asset->type->name}</p>
        <p><strong>Serial Number:</strong> {$asset.serial_number}</p>
        <p><strong>Status:</strong> {$asset.status}</p>
        <p><strong>Purchase Date:</strong> {$asset.purchase_date}</p>
        <p><strong>Warranty End Date:</strong> {$asset.warranty_end_date}</p>
        <p><strong>Description:</strong> {$asset.description}</p>

        <h4>Custom Fields</h4>
        {if $asset->customFields->count()}
            <table class="table">
                {foreach from=$asset->customFields item=customField}
                    <tr>
                        <td><strong>{$customField->customField->field_name}:</strong></td>
                        <td>{$customField->field_value}</td>
                    </tr>
                {/foreach}
            </table>
        {else}
            <p>No custom fields for this asset.</p>
        {/if}
    </div>
</div>

<a href="{$modulelink}&action=assets" class="btn btn-default">Back to Assets</a>