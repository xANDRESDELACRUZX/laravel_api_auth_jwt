<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogEventoError extends Model
{
    use HasFactory;

         // $table Va el nombre dela tabla a la cual se hace referencia
         protected $table = 'log_evento_error';
         // $primaryKey es la primary key definida en la BD
         protected $primaryKey = 'id';
    
         public $timestamps = false;
    
        // Son los campos enla BD
         protected $fillable = [
            "id_log",
            "id_evento",
            "id_entidad",
            "notes",
            "created_at",
            "created_by"
        ];
}