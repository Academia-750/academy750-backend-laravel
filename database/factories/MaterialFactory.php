<?php

namespace Database\Factories;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;


class MaterialFactory extends Factory
{

    public function types(): array
    {
        return ['material', 'recording'];
    }

    public function randomType(): string
    {
        return $this->faker->randomElement($this->types());
    }

    public function definition(): array
    {

        return [
            'name' => $this->faker->regexify('[a-zA-Z\s_-]{5,20}'),
            'type' => $this->randomType(),
            'workspace_id' => config('app.env') === 'documentation' ? 1 : function () {
                return Workspace::factory()->create()->id;
            },
            'created_at' => now(),
            'updated_at' => now(),

        ];
    }

    public function withTags($tags = null): Factory
    {
        return $this->state(function (array $attributes) use ($tags) {
            $tags = $tags ?? $this->faker->words(random_int(1, 3));
            return [
                'tags' => is_array($tags) ? join(',', $tags) : $tags,
            ];
        });
    }

    public function withUrl($url = null): Factory
    {


        return $this->state(function (array $attributes) use ($url) {
            $url = $url ?? $this->faker->url();
            return [
                'url' => $url,
            ];
        });
    }

}