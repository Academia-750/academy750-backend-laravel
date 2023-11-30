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
use Mockery;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;


class StudentLessonDownloadMaterialTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    private $lesson;

    private $material;


    private $tempPath;

    private $secureUrl;
    private $cookie;


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

        $this->material = Material::factory()->withUrl('http://example/myFile.pdf')->create(['type' => 'material']);


        // Our Lesson has 1 student, 1 material and 1 recording
        $this->lesson = Lesson::factory()
            ->withStudents($this->user)
            ->withMaterials([$this->material])
            ->create(['is_active' => true]);


        // Create a temporary folder for files deletion
        $this->tempPath = public_path('/test_temp');

        File::deleteDirectory($this->tempPath);
        File::makeDirectory($this->tempPath);

        // Mock the Watermark to avoid generate a real file
        $mock = $this->partialMock(Watermark::class);
        $mock->shouldReceive('pdf')->once()->andReturn('http://mydomoain.com/temp/file.pdf');


        // We dont call to the API but we need to simulate the request to get the SECURE URL
        $request = \Illuminate\Http\Request::create("api/v1/student-lessons/{$this->material->id}/url", 'GET');
        $request->setUserResolver(function () {
            return $this->user;
        });
        $request->server->add(['REMOTE_ADDR' => '127.0.0.1']);

        [$cookie, $url] = Material::secureURL($request, $this->material->downloadUrl($this->user));
        $this->secureUrl = parse_url($url)['path'];

        $this->disableCookieEncryption(); // Our cookie is already encrypted
        $this->withCookie($cookie->getName(), $cookie->getValue());

    }

    protected function tearDown(): void
    {
        parent::tearDown();
        File::deleteDirectory($this->tempPath);
    }

    /** @test */
    public function not_cookie_attached_404(): void
    {
        // Unset the cookie for this scenario (this works because we disable encryption)
        $this->withCookie('material-download', '');

        $data = $this->get($this->secureUrl)->assertStatus(404);
        $this->assertEquals($data['error'], 'Cookie not found');
    }

    /** @test */
    public function wrong_ip_404(): void
    {
        $payload = ['uuid' => $this->user->uuid, 'ip' => '128.2.32.21'];
        $json = \Crypt::encryptString(json_encode($payload));
        $this->withCookie('material-download', $json);

        $data = $this->get($this->secureUrl)->assertStatus(404);
        $this->assertEquals($data['error'], 'IP doesn`t match');
    }

    /** @test */
    public function user_not_found_404(): void
    {
        $payload = ['uuid' => 'any-uuid', 'ip' => '127.0.0.1'];
        $json = \Crypt::encryptString(json_encode($payload));
        $this->withCookie('material-download', $json);

        $data = $this->get($this->secureUrl)->assertStatus(404);
        $this->assertEquals($data['error'], 'User not found');
    }

    /** @test */
    public function wrong_user_id_404(): void
    {
        $payload = ['uuid' => User::factory()->create()->uuid, 'ip' => '127.0.0.1'];
        $json = \Crypt::encryptString(json_encode($payload));
        $this->withCookie('material-download', $json);

        $data = $this->get($this->secureUrl)->assertStatus(404);
        $this->assertEquals($data['error'], 'User not match');
    }

    /** @test */
    public function token_not_found_404(): void
    {
        $data = $this->get('/api/v1/resource/NOT_FOUND')->assertStatus(404);
        $this->assertEquals($data['error'], 'Token not found');
    }

    /** @test */
    public function token_expired_404(): void
    {
        $token = $this->user->tokens()->where('name', Material::$TOKEN_NAME)->first();
        $token->update(['expires_at' => now()->subMinutes(10)]);

        $data = $this->get($this->secureUrl)->assertStatus(404);
        $this->assertEquals($data['error'], 'Token expired');
    }

    /** @test */
    public function token_belongs_other_user_404(): void
    {
        $other = User::factory()->create();
        $token = $other->createToken(Material::$TOKEN_NAME)->plainTextToken;


        $data = $this->get("/api/v1/resource/$token")->assertStatus(404);
        $this->assertEquals($data['error'], 'User not match');
    }



    /** @test */
    public function download_file_302(): void
    {
        $this->get($this->secureUrl)->assertStatus(302);
    }


    /** @test */
    public function use_token_twice_404(): void
    {
        $this->get($this->secureUrl)->assertStatus(302);
        $this->get($this->secureUrl)->assertStatus(404);
    }


    /** @test */
    public function tokens_are_deleted_302(): void
    {
        $tokensCount = $this->user->tokens()->where('name', Material::$TOKEN_NAME)->count();
        $this->assertEquals($tokensCount, 1);

        $this->get($this->secureUrl)->assertStatus(302);
        $tokensCount = $this->user->tokens()->where('name', Material::$TOKEN_NAME)->count();
        $this->assertEquals($tokensCount, 0);
    }

    /** @test */
    public function only_delete_user_tokens_302(): void
    {
        $other = User::factory()->create();
        $other->createToken(Material::$TOKEN_NAME);


        $this->get($this->secureUrl)->assertStatus(302);

        $tokensCount = $other->tokens()->where('name', Material::$TOKEN_NAME)->count();
        $this->assertEquals($tokensCount, 1);
    }

}
