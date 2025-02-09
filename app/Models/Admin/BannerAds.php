<?php

namespace App\Models\Admin;

use App\Models\BannerAdsAgent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BannerAds extends Model
{
    use HasFactory;

    protected $fillable = [
        'mobile_image',
        'desktop_image',
        'description',
    ];

    protected $appends = ['mobile_image_url', 'desktop_image_url'];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id'); // The admin that owns the banner
    }

    public function getMobileImageUrlAttribute()
    {
        return asset('assets/img/banners_ads/'.$this->mobile_image);
    }

    public function getDesktopImageUrlAttribute()
    {
        return asset('assets/img/banners_ads/'.$this->desktop_image);
    }

    public function bannerAdsAgents()
    {
        return $this->hasMany(BannerAdsAgent::class);
    }

    public function scopeAgent($query)
    {
        return $query->whereHas('bannerAdsAgents', function ($query) {
            $query->where('agent_id', Auth::id());
        });
    }

    public function scopeAgentPlayer($query)
    {
        return $query->whereHas('bannerAdsAgents', function ($query) {
            $query->where('agent_id', Auth::user()->agent_id);
        });
    }

    public function scopeMaster($query)
    {
        $agents = User::find(auth()->user()->id)->agents()->pluck('id')->toArray();

        return $query->whereHas('bannerAdsAgents', function ($query) use ($agents) {
            $query->whereIn('agent_id', $agents);
        });
    }
}
