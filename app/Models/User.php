<?php

namespace App\Models;

use CodeIgniter\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'street_address',
        'city',
        'state',
        'zip_code',
        'password',
        'newsletter',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
}