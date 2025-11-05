<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
    
    protected $fillable = ['organization_id','name','key','description','issue_seq'];

    public function organization(){ return $this->belongsTo(Organization::class); }
    public function boards(){ return $this->hasMany(Board::class); }
    public function issues(){ return $this->hasMany(Issue::class); }

    public function nextIssueKey(): string {
        $this->increment('issue_seq');
        return $this->key . '-' . $this->issue_seq;
    }
}
