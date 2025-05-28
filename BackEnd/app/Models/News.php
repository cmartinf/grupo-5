<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image',
        'category',
        'views',
        'heart_counts',
        'author_id',
        'is_published',
        'published_at',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'news_tag');
    }
    /**
     * Helper to group news by category.
     */
    // public static function groupByCategory()
    // {
    //     return self::published()
    //         ->get()
    //         ->groupBy('category');
    // }
}
