<?php
namespace WHMCS\Module\Addon\AssetManager\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'mod_asset_manager_assets';

    protected $fillable = [
        'userid',
        'asset_type_id',
        'name',
        'description',
        'serial_number',
		'product_number',      // Add this
		'mac_address',         // Add this
'ip_address_type',
'ip_address',
'subnet_mask',        // Add this
'gateway',            // Add this
'dns1',               // Add this
'dns2',               // Add this
'admin_notes',
        'purchase_date',
        'warranty_end_date',
        'status',
        'contact_id',
    ];

    public function client()
    {
        return $this->belongsTo('WHMCS\User\Client', 'userid');
    }

    public function type()
    {
        return $this->belongsTo('WHMCS\Module\Addon\AssetManager\Models\AssetType', 'asset_type_id');
    }

    public function customFields()
    {
        return $this->hasMany('WHMCS\Module\Addon\AssetManager\Models\CustomFieldValue', 'asset_id');
    }
}
