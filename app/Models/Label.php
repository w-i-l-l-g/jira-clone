<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    protected $fillable = ['organization_id','name','color'];

    public function organization(){ return $this->belongsTo(Organization::class); }
    public function issues(){ return $this->belongsToMany(Issue::class, 'issue_label'); }
}