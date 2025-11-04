<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = ['project_id','issue_id','user_id','type','payload'];

    protected $casts = ['payload' => 'array'];

    public function project(){ return $this->belongsTo(Project::class); }
    public function issue(){ return $this->belongsTo(Issue::class); }
    public function user(){ return $this->belongsTo(User::class); }
}