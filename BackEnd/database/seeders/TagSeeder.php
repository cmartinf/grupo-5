<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    public function run()
    {
        $tags = ['Política', 'Negocios', 'Salud', 'Educación', 'Ciencia', 
                 'Tecnología', 'Deportes', 'Entretenimiento', 'Cultura', 'Opinión', 
                 'Internacional', 'Local', 'Economía', 'Medio Ambiente'];
        foreach ($tags as $tag) {
            Tag::create(['name' => $tag]);
        }
    }
}
