<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Concerns\Filterable;

class ToDoList extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'name', 'date','user_id'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}