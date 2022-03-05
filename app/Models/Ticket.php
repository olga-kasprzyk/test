<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TraitUuid;

class Ticket extends Model
{
    use HasFactory;
    use TraitUuid;

    protected $fillable = [
      'subject',
      'content',
      'author',
      'email',
      'status',
      'processed_at'
    ];

    protected $casts = [
      'id' => 'string',
      'status' => 'boolean'
    ];

    /**
     * Scope a query to only include open tickets.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeOpen($query){
        return $query->where('status', '!=', true);
    }

    /**
     * Scope a query to only include processed tickets.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeClosed($query){
        return $query->where('status', '=', true);
    }

    /**
     * Scope a query to only include tickets for a particular user by email.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeEmail($query, $email){
        return $query->where('email', '=', $email);
    }
}
