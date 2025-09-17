<?php
namespace WHMCS\Module\Addon\AssetManager\Models;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    public $timestamps = false;
	
	protected $table = 'mod_asset_manager_custom_fields';

    protected $fillable = [
        'asset_type_id',
        'field_name',
        'field_type',
        'field_options',
    ];

    public function assetType()
    {
        return $this->belongsTo('WHMCS\Module\Addon\AssetManager\Models\AssetType', 'asset_type_id');
    }
}
