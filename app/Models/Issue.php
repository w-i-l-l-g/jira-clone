<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $fillable = [
        'project_id','column_id','reporter_id','assignee_id',
        'key','title','description','priority','estimate'
    ];

    public function project(){ return $this->belongsTo(Project::class); }
    public function column(){ return $this->belongsTo(Column::class); }
    public function reporter(){ return $this->belongsTo(User::class, 'reporter_id'); }
    public function assignee(){ return $this->belongsTo(User::class, 'assignee_id'); }
    public function labels(){ return $this->belongsToMany(Label::class, 'issue_label'); }
    public function comments(){ return $this->hasMany(Comment::class); }
}