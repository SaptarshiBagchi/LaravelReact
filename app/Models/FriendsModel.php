<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FriendsModel extends Model
{
    use HasFactory;
    protected $table = 'friends';
    protected $casts = [
        'created_at' => 'datetime:d F Y g:i a.',
        'updated_at' => 'datetime:d F Y g:i a.'
    ];

    public function toUser()
    {
        //just so that the people cannot access the times for this user
        return $this->belongsTo(User::class, 'to_user_id', 'id')->select(array('id', 'name', 'email'));
    }

    public function fromUser()
    {
        //just so that the people cannot access the times for this user
        return $this->belongsTo(User::class, 'from_user_id', 'id')->select(array('id', 'name', 'email'));
    }

    public function statusText()
    {
        return $this->belongsTo(StatusText::class, 'status', 'id')->select(array('id', 'status_text'));
    }
}
