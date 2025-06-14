<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Author extends Model
{
    public $timestamps = false;

    // Nama kolom primary key
    protected $primaryKey = 'author_id';

    // Tipe primary key bukan integer (karena ULID string)
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang bisa diisi massal
    protected $fillable = ['name', 'nationality', 'birthdate'];

    // Tambahkan boot method agar ULID otomatis di-generate
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::ulid();
            }
        });
    }
}
