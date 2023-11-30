<?php

namespace Tests\Feature;

use App;
use App\Console\Commands\TemporalFileClean;
use App\Core\Resources\Watermark\Watermark;
use App\Models\Lesson;
use App\Models\Material;
use App\Models\Permission;
use App\Models\User;
use File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;


class StudentLessonsMaterialURLTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    private $lesson;

    private $material;

    private $recording;

    private $tempPath;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->student()->allowedTo([
            Permission::SEE_LESSON_MATERIALS,
            Permission::SEE_LESSON_RECORDINGS,
        ])->create();

        $this->material = Material::factory()->withUrl()->create(['type' => 'material']);
        $this->recording = Material::factory()->withUrl()->create(['type' => 'recording']);


        // Our Lesson has 1 student, 1 material and 1 recording
        $this->lesson = Lesson::factory()
            ->withStudents($this->user)
            ->withMaterials([$this->material, $this->recording])
            ->create(['is_active' => true]);

        $this->actingAs($this->user);


        // Create a temporary folder for files deletion
        $this->tempPath = public_path('/test_temp');

        File::deleteDirectory($this->tempPath);
        File::makeDirectory($this->tempPath);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        File::deleteDirectory($this->tempPath);
    }

    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/student-lessons/{$this->material->id}/url")->assertStatus(401);
    }


    /** @test */
    public function no_permissions_403(): void
    {

        $user = User::factory()->student()->create(); // Missing SEE LESSONS
        $this->actingAs($user)->get("api/v1/student-lessons/{$this->material->id}/url")->assertStatus(403);

        $user->permissions()->detach();
        $user->givePermissionTo(Permission::SEE_LESSON_RECORDINGS); // Wrong Permission type
        $this->actingAs($user)->get("api/v1/student-lessons/{$this->material->id}/url")->assertStatus(403);

        $user->permissions()->detach();
        $user->givePermissionTo(Permission::SEE_LESSON_MATERIALS); // Wrong Permission type
        $this->actingAs($user)->get("api/v1/student-lessons/{$this->recording->id}/url")->assertStatus(403);

    }

    /** @test */
    public function not_found_404(): void
    {
        $this->get("api/v1/student-lessons/404/url")->assertStatus(404);
    }

    /** @test */
    public function material_not_in_lesson_409(): void
    {
        $material = Material::factory()->create();
        $this->get("api/v1/student-lessons/{$material->id}/url")->assertStatus(409);
    }

    /** @test */
    public function material_in_lesson_not_active_409(): void
    {
        $this->lesson->is_active = false;
        $this->lesson->save();

        $this->get("api/v1/student-lessons/{$this->material->id}/url")->assertStatus(409);
    }

    /** @test */
    public function download_recording_url_200(): void
    {
        $data = $this->get("api/v1/student-lessons/{$this->recording->id}/url")->assertStatus(200);
        $this->assertEquals($data['url'], $this->recording->url);
    }

    /** @test */
    public function download_material_type_200(): void
    {
        $url = 'http://test.url';
        $this->material->url = $url; // This type will be defined to UNKONW
        $this->material->save();

        $data = $this->get("api/v1/student-lessons/{$this->material->id}/url")->assertStatus(200);

        // URL is generated and saved in a token

        $token = $this->user->tokens()->where('name', Material::$TOKEN_NAME)->first();
        $this->assertEquals($token['abilities'][0], $url);
        $this->assertEquals($token['id'], $this->user->id);

        // SECURE URL is returned
        $this->assertStringContainsString('/api/v1/resource/' . $token->plainTextToken, $data['url']);
        $data->assertCookie(Material::$TOKEN_NAME);
    }


    /** @test */
    public function admin_can_download_too_200(): void
    {
        $url = 'http://test.url';
        $this->material->url = $url; // This type will be defined to UNKONW
        $this->material->save();

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get("api/v1/student-lessons/{$this->recording->id}/url")->assertStatus(200);
        $this->actingAs($admin)->get("api/v1/student-lessons/{$this->material->id}/url")->assertStatus(200);
    }
    /** @test */
    public function download_pdf_watermark_200(): void
    {
        $mock = $this->partialMock(Watermark::class);
        $mock->shouldReceive('pdf')->once()->andReturn('internal-url');

        $this->material->url = 'http://test.url/mypdf.pdf';
        $this->material->save();

        $this->get("api/v1/student-lessons/{$this->material->id}/url")->assertStatus(200);
        $token = $this->user->tokens()->where('name', Material::$TOKEN_NAME)->first();
        $this->assertEquals($token['abilities'][0], 'internal-url');
        $this->assertEquals($token['id'], $this->user->id);
    }

    /** @test */
    public function handle_download_error_424(): void
    {
        $mock = $this->partialMock(Watermark::class);
        $mock->shouldReceive('pdf')->once()->andThrow(new \Exception);

        $this->material->url = 'http://test.url/mypdf.pdf';
        $this->material->save();

        $this->get("api/v1/student-lessons/{$this->material->id}/url")->assertStatus(424);
    }

    /** @test */
    public function download_image_watermark_200(): void
    {
        $mock = $this->partialMock(Watermark::class);
        $mock->shouldReceive('image')->once()->andReturn('internal-image-url');

        $this->material->url = 'http://test.url/mypdf.png';
        $this->material->save();

        $this->get("api/v1/student-lessons/{$this->material->id}/url")->assertStatus(200);

        $token = $this->user->tokens()->where('name', Material::$TOKEN_NAME)->first();
        $this->assertEquals($token['abilities'][0], 'internal-image-url');
        $this->assertEquals($token['id'], $this->user->id);
    }

    /** @test */
    public function temp_folder_not_exists_500(): void
    {
        // Create an instance of the FileCleanupService
        $service = new TemporalFileClean();

        // Call the cleanUpTemporalFolder method
        $result = $service->deleteFiles(public_path('/other'));
        $this->assertFalse($result);
    }

    /** @test */
    public function not_delete_new_temporal_files_200(): void
    {

        // Create a test file in the folder
        $testFilePath = $this->tempPath . '/test.txt';
        file_put_contents($testFilePath, 'Test file content');

        // Create an instance of the FileCleanupService
        $service = new TemporalFileClean();

        // Call the cleanUpTemporalFolder method
        $result = $service->deleteFiles($this->tempPath);
        $this->assertTrue($result);
        $this->assertFileExists($testFilePath);
    }

    /** @test */
    public function delete_old_temporal_files_200(): void
    {
        // Create a test file in the folder
        $testFilePath = $this->tempPath . '/test.txt';
        file_put_contents($testFilePath, 'Test file content');

        // Create an instance of the FileCleanupService
        $this->travel(3)->hours();
        $service = new TemporalFileClean();

        // Call the cleanUpTemporalFolder method
        $result = $service->deleteFiles($this->tempPath);
        $this->assertTrue($result);
        $this->assertFileDoesNotExist($testFilePath);
    }
}
