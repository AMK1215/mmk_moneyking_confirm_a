<?php

namespace App\Models\Admin;

use App\Models\PromotionAgent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'image', 'agent_id', 'description',
    ];

    protected $appends = ['img_url'];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id'); // The admin that owns the banner
    }

    public function getImgUrlAttribute()
    {
        return asset('assets/img/promotions/'.$this->image);
    }

    public function promotionAgents()
    {
        return $this->hasMany(PromotionAgent::class);
    }

    public function scopeAgent($query)
    {
        return $query->whereHas('promotionAgents', function ($query) {
            $query->where('agent_id', Auth::id());
        });
    }

    public function scopeAgentPlayer($query)
    {
        return $query->whereHas('promotionAgents', function ($query) {
            $query->where('agent_id', Auth::user()->agent_id);
        });
    }

    public function scopeMaster($query)
    {
        $agents = User::find(auth()->user()->id)->agents()->pluck('id')->toArray();

        return $query->whereHas('promotionAgents', function ($query) use ($agents) {
            $query->whereIn('agent_id', $agents);
        });
    }
}
