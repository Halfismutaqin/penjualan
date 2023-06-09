<?php
 
namespace App\Models;
 
use CodeIgniter\Model;
 
class Login extends Model
{
    protected $table            = 'login';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = false; 

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';    
 
}