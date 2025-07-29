<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $table = 'backup';
    protected $primaryKey = 'BackupID';
    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'UsuarioID');
    }
}
