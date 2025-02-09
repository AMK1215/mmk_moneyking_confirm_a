<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerTextAgent extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'agent_id', 'banner_text_id'];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
