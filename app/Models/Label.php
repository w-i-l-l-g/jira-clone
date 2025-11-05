<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Label extends Model
{
    use HasFactory;
    
    protected $fillable = ['organization_id','name','color'];

    public function organization(){ return $this->belongsTo(Organization::class); }
    public function issues(){ return $this->belongsToMany(Issue::class, 'issue_label'); }
}