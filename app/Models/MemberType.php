<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberType extends Model
{
    protected $table = 'mst_member_type';
    protected $primaryKey = 'member_type_id';
    public $timestamps = false;

    protected $fillable = [
        'member_type_name',
        'loan_limit',
        'loan_periode',
        'enable_reserve',
        'reserve_limit',
        'member_periode',
        'reborrow_limit',
        'fine_each_day',
        'grace_periode',
        'input_date',
        'last_update',
    ];

    public function members()
    {
        return $this->hasMany(Member::class, 'member_type_id');
    }
}
