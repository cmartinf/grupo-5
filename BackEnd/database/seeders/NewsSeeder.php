<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;

class NewsSeeder extends Seeder
{
    public function run()
    {
        News::create([
            'title' => 'Último Momento: Laravel es Increíble',
            'content' => 'Laravel es uno de los mejores frameworks de PHP.',
            'image' => 'noticias/laravel.jpg',
            'category' => 'Política',
            'author_id' => 1,
            'heart_counts' => 100,
        ]);

        News::create([
            'title' => 'VueJS está en auge',
            'content' => 'VueJS se está convirtiendo en un framework frontend muy popular.',
            'image' => 'noticias/vuejs.jpg',
            'category' => 'Política',
            'author_id' => 2,
            'heart_counts' => 150,
        ]);
    }
}

