<?php
namespace WHMCS\Module\Addon\AssetManager\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFieldValue extends Model
{
    public $timestamps = false;
	protected $table = 'mod_asset_manager_custom_field_values';

    protected $fillable = [
        'asset_id',
        'custom_field_id',
        'field_value',
    ];

    public function customField()
    {
        return $this->belongsTo('WHMCS\Module\Addon\AssetManager\Models\CustomField', 'custom_field_id');
    }

    public function asset()
    {
        return $this->belongsTo('WHMCS\Module\Addon\AssetManager\Models\Asset', 'asset_id');
    }
}
