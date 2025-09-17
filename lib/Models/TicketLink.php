<?php
namespace WHMCS\Module\Addon\AssetManager\Models;

use Illuminate\Database\Eloquent\Model;

class TicketLink extends Model
{
    protected $table = 'mod_asset_manager_ticket_links';

    protected $fillable = [
        'ticket_id',
        'asset_id',
    ];

    public $timestamps = false;

    public function asset()
    {
        return $this->belongsTo('WHMCS\Module\Addon\AssetManager\Models\Asset', 'asset_id');
    }
}
