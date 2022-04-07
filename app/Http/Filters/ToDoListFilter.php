<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class ToDoListFilter extends Filter
{
    /**
     * Filter the lists by the given string.
     *
     * @param  string|null  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function name(string $value = null): Builder
    {
        return $this->builder->where('name', 'like', "%{$value}%");
    }

    /**
     * Filter the lists by the given date.
     *
     * @param  string|null  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function date(string $value = null): Builder
    {
        return $this->builder->where('date', $value);
    }
}