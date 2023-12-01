<?php

namespace App\Models;

use App\Core\Resources\Storage\Storage;
use App\Core\Resources\Watermark\Watermark;
use DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Carbon;



class Material extends Model
{

    use HasFactory;

    public static function allowedTypes()
    {
        return ['material', 'recording'];
    }


    protected $fillable = [
        'name',
        'type',
        'url',
        'tags',
        'workspace_id'
    ];

    protected $attributes = [
        'tags' => '',
        'url' => ''
    ];


    // Relationships methods
    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }

    // Relationships methods
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class)->withTimestamps();
    }

    public static function deleteFromStorage($material)
    {
        if (!$material->url) {
            return ['status' => 204, 'message' => 'No Action'];
        }

        if ($material->type !== 'material') {
            return ['status' => 204, 'message' => 'No Action'];
        }

        return Storage::for($material)->deleteFile($material); // Delete the old one.
    }


    public function canDownload(User $user)
    {
        // Admin can call this API to see how the material is water mark too
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($this->type === 'recording') {
            return $user->can(Permission::SEE_LESSON_RECORDINGS);
        }

        return $user->can(Permission::SEE_LESSON_MATERIALS);
    }
    public function downloadUrl(User $user)
    {

        if ($this->type === 'recording') {
            return $this->url;
        }

        // else $this->type === 'material'

        $docType = $this->getDocumentTypeFromURL();

        if ($docType === DocumentType::PDF) {
            return Watermark::get()->pdf($this->url, $this->name, $user);
        }
        if ($docType === DocumentType::IMAGE) {
            return Watermark::get()->image($this->url, $this->name, $user);
        }

        // Other Doc type
        return $this->url;
    }




    public function getDocumentTypeFromURL()
    {
        return getDocumentTypeFromURL($this->url);
    }

    public static $TOKEN_NAME = 'material-download';

    /**
     * @return [$cookie, $url]
     */
    public static function secureURL(\Illuminate\Http\Request $request, $url)
    {
        $seconds = 10; // 10 seconds

        // Generate Single Use token
        $request->user()->tokens()->where('name', "download-material")->delete();
        $token = $request->user()->createToken(self::$TOKEN_NAME, [$url], now()->addSeconds($seconds))->plainTextToken;
        $url = asset("api/v1/resource/$token", app()->environment() !== 'local');

        // Generate Cookie
        $payload = ['uuid' => $request->user()->uuid, 'ip' => $request->ip()];
        $json = \Crypt::encryptString(json_encode($payload));
        $cookie = \Cookie::make(self::$TOKEN_NAME, $json, $seconds / 60)->withSameSite('None');
        $cookie->setSecureDefault(true);

        \Log::debug($cookie);

        return [$cookie, $url];
    }

    /**
     * @return [$url, $error]
     */
    public static function unlockSafeURL(\Illuminate\Http\Request $request, $code)
    {
        $token = PersonalAccessToken::findToken($code);

        if (!$token) {
            return ['', "Token not found"];
        }
        if (Carbon::now()->greaterThan($token->expires_at)) {
            return ['', "Token expired"];

        }
        // This cookie is left by secureURL to identify that the user which is calling
        // is the same that the one who generate the request
        $payload = $request->cookie(self::$TOKEN_NAME);
        if (!$payload) {
            return ['', "Cookie not found"];
        }

        $json = \Crypt::decryptString($payload);
        ['uuid' => $uuid, 'ip' => $ip] = json_decode($json, true);


        if ($ip !== $request->ip()) {
            return ['', 'IP doesn`t match'];
        }

        $user = User::query()->where('uuid', $uuid)->first();
        if (!$user) {
            return ['', 'User not found'];
        }

        if ($token->tokenable_id !== $user->id) {
            return ['', 'User not match'];
        }


        $user->tokens()->where('name', self::$TOKEN_NAME)->delete();
        $originalUrl = $token->abilities[0];
        return [$originalUrl, ''];
    }
}
