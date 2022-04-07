<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Relations\Pivot;
 
class TagTask extends Pivot
{
    protected $fillable = [
        'task_id', 'tag_id'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public $incrementing = true;
}