<?php

namespace App\Models\Admin;

use App\Models\BannerAgent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'mobile_image',
        'desktop_image',
        'agent_id',
        'admin_id',
    ];

    protected $appends = ['mobile_image_url', 'desktop_image_url'];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function getMobileImageUrlAttribute()
    {
        return asset('assets/img/banners/'.$this->mobile_image);
    }

    public function getDesktopImageUrlAttribute()
    {
        return asset('assets/img/banners/'.$this->desktop_image);
    }

    public function bannerAgents()
    {
        return $this->hasMany(BannerAgent::class);
    }

    public function scopeAgent($query)
    {
        return $query->whereHas('bannerAgents', function ($query) {
            $query->where('agent_id', Auth::id());
        });
    }

    public function scopeAgentPlayer($query)
    {
        return $query->whereHas('bannerAgents', function ($query) {
            $query->where('agent_id', Auth::user()->agent_id);
        });
    }

    public function scopeMaster($query)
    {
        $agents = User::find(auth()->user()->id)->agents()->pluck('id')->toArray();

        return $query->whereHas('bannerAgents', function ($query) use ($agents) {
            $query->whereIn('agent_id', $agents);
        });
    }
}
