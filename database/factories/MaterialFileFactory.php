<?php

namespace Database\Factories;

use App\Models\Material;
use App\Models\MaterialFile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MaterialFile>
 */
class MaterialFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $extension = fake()->randomElement(['pdf', 'docx', 'pptx', 'zip', 'png']);

        return [
            'material_id' => Material::factory(),
            'disk' => config('materials.disk'),
            'path' => 'materials/'.Str::uuid().'.'.$extension,
            'original_name' => fake()->word().'.'.$extension,
            'mime_type' => fake()->randomElement([
                'application/pdf',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/zip',
                'image/png',
            ]),
            'extension' => $extension,
            'size' => fake()->numberBetween(1024, 5 * 1024 * 1024),
            'checksum' => null,
            'sort_order' => 0,
        ];
    }
}
