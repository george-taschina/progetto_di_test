<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Concerns\Filterable;

class Task extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'name', 'start','end','to_do_list_id'
    ];

    public function to_do_list()
    {
        return $this->belongsTo(ToDoList::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

}