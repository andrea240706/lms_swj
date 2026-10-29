<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\MaterialFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MaterialManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_material(): void
    {
        $response = $this->post(route('guru.materi.store'), $this->materialPayload());

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('materials', 0);
    }

    public function test_admin_can_create_material(): void
    {
        Storage::fake('local');
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->from('/dashboard')
            ->post(route('guru.materi.store'), $this->materialPayload());

        $response->assertRedirect('/dashboard')->assertSessionHasNoErrors();
        $this->assertDatabaseHas('materials', ['created_by' => $admin->id]);
    }

    public function test_student_cannot_create_material(): void
    {
        $student = User::factory()->create(['role' => 'siswa']);

        $response = $this->actingAs($student)->post(route('guru.materi.store'), $this->materialPayload());

        $response->assertForbidden();
        $this->assertDatabaseCount('materials', 0);
    }

    public function test_teacher_can_create_material_with_multiple_files(): void
    {
        Storage::fake('local');
        $teacher = $this->teacher();

        $response = $this->actingAs($teacher)
            ->from('/dashboard')
            ->post(route('guru.materi.store'), $this->materialPayload([
                'files' => [
                    UploadedFile::fake()->create('modul.pdf', 100, 'application/pdf'),
                    UploadedFile::fake()->create('slide.pptx', 200, 'application/vnd.openxmlformats-officedocument.presentationml.presentation'),
                ],
            ]));

        $response->assertRedirect('/dashboard')->assertSessionHasNoErrors();

        $material = Material::query()->firstOrFail();

        $this->assertSame($teacher->id, $material->created_by);
        $this->assertSame('pwpb', $material->mapel_key);
        $this->assertSame(1, $material->meeting_number);
        $this->assertTrue($material->is_published);
        $this->assertCount(2, $material->files);

        foreach ($material->files as $file) {
            Storage::disk('local')->assertExists($file->path);
        }

        $this->assertSame(
            ['modul.pdf', 'slide.pptx'],
            $material->files->pluck('original_name')->all()
        );
    }

    public function test_material_rejects_disallowed_extension(): void
    {
        Storage::fake('local');
        $teacher = $this->teacher();

        $response = $this->actingAs($teacher)
            ->from('/dashboard')
            ->post(route('guru.materi.store'), $this->materialPayload([
                'files' => [
                    UploadedFile::fake()->create('malware.exe', 10, 'application/x-msdownload'),
                ],
            ]));

        $response
            ->assertRedirect('/dashboard')
            ->assertSessionHasErrors(['files.0' => 'Format file .exe tidak diizinkan.']);

        $this->assertDatabaseCount('materials', 0);
    }

    public function test_material_rejects_executable_content_disguised_as_pdf(): void
    {
        Storage::fake('local');
        $teacher = $this->teacher();

        $response = $this->actingAs($teacher)
            ->from('/dashboard')
            ->post(route('guru.materi.store'), $this->materialPayload([
                'files' => [
                    UploadedFile::fake()->create('modul.pdf', 10, 'application/x-msdownload'),
                ],
            ]));

        $response
            ->assertRedirect('/dashboard')
            ->assertSessionHasErrors(['files.0' => 'File executable tidak diizinkan.']);

        $this->assertDatabaseCount('materials', 0);
    }

    public function test_material_rejects_content_that_does_not_match_its_extension(): void
    {
        Storage::fake('local');
        $teacher = $this->teacher();

        $response = $this->actingAs($teacher)
            ->from('/dashboard')
            ->post(route('guru.materi.store'), $this->materialPayload([
                'files' => [
                    UploadedFile::fake()->create('modul.pdf', 10, 'image/png'),
                ],
            ]));

        $response
            ->assertRedirect('/dashboard')
            ->assertSessionHasErrors(['files.0' => 'Isi file tidak sesuai dengan format .pdf.']);

        $this->assertDatabaseCount('materials', 0);
    }

    public function test_material_rejects_too_many_files(): void
    {
        Storage::fake('local');
        $teacher = $this->teacher();

        $files = collect(range(1, 11))
            ->map(fn (int $index): UploadedFile => UploadedFile::fake()->create("modul-{$index}.pdf", 1, 'application/pdf'))
            ->all();

        $response = $this->actingAs($teacher)
            ->from('/dashboard')
            ->post(route('guru.materi.store'), $this->materialPayload(['files' => $files]));

        $response->assertRedirect('/dashboard')->assertSessionHasErrors('files');
        $this->assertDatabaseCount('materials', 0);
    }

    public function test_teacher_can_update_material_metadata(): void
    {
        $teacher = $this->teacher();
        $material = Material::factory()->create([
            'created_by' => $teacher->id,
            'title' => 'Materi Lama',
        ]);

        $response = $this->actingAs($teacher)
            ->from('/dashboard')
            ->patch(route('guru.materi.update', $material), [
                'title' => 'Materi Baru',
                'description' => 'Deskripsi baru',
                'is_published' => false,
            ]);

        $response->assertRedirect('/dashboard')->assertSessionHasNoErrors();

        $material->refresh();

        $this->assertSame('Materi Baru', $material->title);
        $this->assertSame('Deskripsi baru', $material->description);
        $this->assertFalse($material->is_published);
        $this->assertSame($teacher->id, $material->updated_by);
    }

    public function test_student_cannot_update_material(): void
    {
        $student = User::factory()->create(['role' => 'siswa']);
        $material = Material::factory()->create(['title' => 'Materi Lama']);

        $response = $this->actingAs($student)
            ->patch(route('guru.materi.update', $material), [
                'title' => 'Materi Siswa',
            ]);

        $response->assertForbidden();
        $this->assertSame('Materi Lama', $material->fresh()->title);
    }

    public function test_teacher_can_add_files_to_existing_material(): void
    {
        Storage::fake('local');
        $teacher = $this->teacher();
        $material = Material::factory()->create(['created_by' => $teacher->id]);
        MaterialFile::factory()->for($material)->create([
            'path' => 'materials/existing.pdf',
            'original_name' => 'existing.pdf',
            'extension' => 'pdf',
            'size' => 1024,
        ]);

        $response = $this->actingAs($teacher)
            ->from('/dashboard')
            ->post(route('guru.materi.files.store', $material), [
                'files' => [
                    UploadedFile::fake()->create('tambahan.pdf', 100, 'application/pdf'),
                ],
            ]);

        $response->assertRedirect('/dashboard')->assertSessionHasNoErrors();
        $this->assertCount(2, $material->files()->get());

        $newFile = $material->files()->where('original_name', 'tambahan.pdf')->firstOrFail();
        Storage::disk('local')->assertExists($newFile->path);
    }

    public function test_teacher_can_delete_one_file_but_not_the_last_file(): void
    {
        Storage::fake('local');
        $teacher = $this->teacher();
        $material = Material::factory()->create(['created_by' => $teacher->id]);

        $firstFile = $material->files()->create($this->fileAttributes('materials/first.pdf', 'first.pdf'));
        $secondFile = $material->files()->create($this->fileAttributes('materials/second.pdf', 'second.pdf'));

        Storage::disk('local')->put($firstFile->path, 'first');
        Storage::disk('local')->put($secondFile->path, 'second');

        $this->actingAs($teacher)
            ->from('/dashboard')
            ->delete(route('guru.materi.files.destroy', [$material, $firstFile]))
            ->assertRedirect('/dashboard')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('material_files', ['id' => $firstFile->id]);
        Storage::disk('local')->assertMissing($firstFile->path);

        $this->actingAs($teacher)
            ->from('/dashboard')
            ->delete(route('guru.materi.files.destroy', [$material, $secondFile]))
            ->assertRedirect('/dashboard')
            ->assertSessionHasErrors('file');

        $this->assertDatabaseHas('material_files', ['id' => $secondFile->id]);
        Storage::disk('local')->assertExists($secondFile->path);
    }

    public function test_teacher_can_delete_material_and_all_of_its_files(): void
    {
        Storage::fake('local');
        $teacher = $this->teacher();
        $material = Material::factory()->create(['created_by' => $teacher->id]);
        $file = $material->files()->create($this->fileAttributes('materials/delete-me.pdf', 'delete-me.pdf'));

        Storage::disk('local')->put($file->path, 'content');

        $this->actingAs($teacher)
            ->from('/dashboard')
            ->delete(route('guru.materi.destroy', $material))
            ->assertRedirect('/dashboard')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('materials', ['id' => $material->id]);
        $this->assertDatabaseMissing('material_files', ['id' => $file->id]);
        Storage::disk('local')->assertMissing($file->path);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function materialPayload(array $overrides = []): array
    {
        return array_merge([
            'mapel_key' => 'pwpb',
            'meeting_number' => 1,
            'title' => 'Modul Laravel',
            'description' => 'Materi pertemuan pertama.',
            'is_published' => true,
            'files' => [
                UploadedFile::fake()->create('modul.pdf', 100, 'application/pdf'),
            ],
        ], $overrides);
    }

    /**
     * @return array<string, mixed>
     */
    private function fileAttributes(string $path, string $originalName): array
    {
        return [
            'disk' => 'local',
            'path' => $path,
            'original_name' => $originalName,
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'size' => 1024,
            'checksum' => null,
            'sort_order' => 0,
        ];
    }

    private function teacher(): User
    {
        return User::factory()->create(['role' => 'guru']);
    }
}
