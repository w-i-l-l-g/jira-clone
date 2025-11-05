<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organization extends Model
{
    use HasFactory;
    
    protected $fillable = ['owner_id','name','slug'];

    public function users() { return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps(); }
    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }
    public function projects() { return $this->hasMany(Project::class); }
}