<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes; // softdeletes 가져옴

class Boards extends Model
{
    use HasFactory, SoftDeletes; //! softdelete 사용해줄 때 추가해주기

    protected $guarded = ['id', 'created_at']; // blacklist

    protected $dates = ['deleted_at'];
}
