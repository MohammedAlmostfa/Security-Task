<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{

    use HasFactory,SoftDeletes;

    protected $fillable = [
         'body', 'commentable_id', 'commentable_type'
     ];
    public function commentable()
    {
        return $this->morphTo();
    }
}
