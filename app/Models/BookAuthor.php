<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BookAuthor extends Model
{
    use HasFactory;

    public $timestamps = false;

    // Nama tabel secara eksplisit
    protected $table = 'book_authors';

    // Primary key custom dengan ULID
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang bisa diisi
    protected $fillable = [
        'id',
        'book_id',
        'author_id',
    ];

    // Auto-generate ULID saat membuat data baru
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    // Relasi ke Book
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id', 'book_id');
    }

    // Relasi ke Author
    public function author()
    {
        return $this->belongsTo(Author::class, 'author_id', 'author_id');
    }
}
