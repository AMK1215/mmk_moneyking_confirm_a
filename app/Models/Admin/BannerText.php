<?php

namespace App\Models\Admin;

use App\Models\BannerTextAgent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BannerText extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'agent_id',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function bannerTextAgents()
    {
        return $this->hasMany(BannerTextAgent::class);
    }

    public function scopeAgent($query)
    {
        return $query->whereHas('bannerTextAgents', function ($query) {
            $query->where('agent_id', Auth::id());
        });
    }

    public function scopeAgentPlayer($query)
    {
        return $query->whereHas('bannerTextAgents', function ($query) {
            $query->where('agent_id', Auth::user()->agent_id);
        });
    }

    public function scopeMaster($query)
    {
        $agents = User::find(auth()->user()->id)->agents()->pluck('id')->toArray();

        return $query->whereHas('bannerTextAgents', function ($query) use ($agents) {
            $query->whereIn('agent_id', $agents);
        });
    }
}
