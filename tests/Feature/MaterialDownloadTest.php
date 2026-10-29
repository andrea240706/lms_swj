<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\MaterialFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MaterialDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_download_material_file(): void
    {
        [$material, $file] = $this->materialWithStoredFile();

        $response = $this->get(route('materials.files.download', [$material, $file]));

        $response->assertRedirect(route('login'));
    }

    public function test_teacher_can_download_draft_material_file(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);
        [$material, $file] = $this->materialWithStoredFile(['is_published' => false]);

        $response = $this->actingAs($teacher)->get(route('materials.files.download', [$material, $file]));

        $response
            ->assertOk()
            ->assertDownload('modul.pdf')
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('x-content-type-options', 'nosniff');
    }

    public function test_student_can_download_published_material_file(): void
    {
        $student = User::factory()->create(['role' => 'siswa']);
        [$material, $file] = $this->materialWithStoredFile(['is_published' => true]);

        $response = $this->actingAs($student)->get(route('materials.files.download', [$material, $file]));

        $response->assertOk()->assertDownload('modul.pdf');
    }

    public function test_student_cannot_download_draft_material_file(): void
    {
        $student = User::factory()->create(['role' => 'siswa']);
        [$material, $file] = $this->materialWithStoredFile(['is_published' => false]);

        $response = $this->actingAs($student)->get(route('materials.files.download', [$material, $file]));

        $response->assertForbidden();
    }

    public function test_download_returns_404_when_file_belongs_to_another_material(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);
        [$material] = $this->materialWithStoredFile();
        [, $otherFile] = $this->materialWithStoredFile();

        $response = $this->actingAs($teacher)->get(
            route('materials.files.download', [$material, $otherFile])
        );

        $response->assertNotFound();
    }

    public function test_download_returns_404_when_stored_file_is_missing(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);
        [$material, $file] = $this->materialWithStoredFile();

        Storage::disk('local')->delete($file->path);

        $response = $this->actingAs($teacher)->get(route('materials.files.download', [$material, $file]));

        $response->assertNotFound();
    }

    /**
     * @param  array<string, mixed>  $materialOverrides
     * @return array{0: Material, 1: MaterialFile}
     */
    private function materialWithStoredFile(array $materialOverrides = []): array
    {
        Storage::fake('local');

        $material = Material::factory()->create($materialOverrides);
        $file = $material->files()->create([
            'disk' => 'local',
            'path' => 'materials/'.uniqid().'.pdf',
            'original_name' => 'modul.pdf',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'size' => 1024,
            'checksum' => null,
            'sort_order' => 0,
        ]);

        Storage::disk('local')->put($file->path, 'materi pdf');

        return [$material, $file];
    }
}
