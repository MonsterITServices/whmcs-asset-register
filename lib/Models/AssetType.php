<?php
namespace WHMCS\Module\Addon\AssetManager\Models;

use Illuminate\Database\Eloquent\Model;

class AssetType extends Model
{
	public $timestamps = false;
    protected $table = 'mod_asset_manager_types';

    protected $fillable = [
        'name',
    ];

    public function customFields()
    {
        return $this->hasMany('WHMCS\Module\Addon\AssetManager\Models\CustomField', 'asset_type_id');
    }
}
