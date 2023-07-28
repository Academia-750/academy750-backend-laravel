<?php

namespace Database\Seeders;

use App\Models\Subtopic;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubtopicTopicSeeder extends Seeder
{
    public $subtopics;

    public function run(): void
    {
        $this->subtopics = Subtopic::all();

        // el método cursor evita el desbordamiento de memoria en caso de haber muchos registros que excedan el limite de memoria
        // tambien podemos usar el metodo chunk o chunkById (para usarlo con filtros)
        foreach (Topic::query()->cursor() as $topic) {
            $topic->subtopics()->sync(
                $this->getRandomSubtopics()
            );
        }
    }

    public function getRandomSubtopics (): array {
        $subtopics = collect([]);
        $optionsAvailable = [ 'yes', 'no' ];

        foreach ( range(1, random_int(5, 40)) as $number) {

            $subtopics->put($this->subtopics->random()->getKey(), [
                'is_available' => $optionsAvailable[random_int(0,1)]
            ]);
        }

        return $subtopics->toArray();
    }
}
