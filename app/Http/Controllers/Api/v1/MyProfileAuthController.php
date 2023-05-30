<?php

namespace App\Http\Controllers\Api\v1;

use App\Core\Services\ManageImagesStorage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Users\UpdateImageAccountRequest;
use App\Http\Resources\Api\User\v1\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MyProfileAuthController extends Controller
{
    public function get_data_my_profile(): UserResource
    {
        $user = User::applyIncludes()->find(auth()->user()->getKey());

        return UserResource::make($user);
    }

    public function updateImageAccount (UpdateImageAccountRequest $request): \Illuminate\Http\JsonResponse
    {

        $this->deleteImageModelInStorage();

        $path = Storage::url(
            $request->file('image')?->store('public/users/images')
        );

        if (!Auth::user()?->image) {
            Auth::user()?->image()->create([
                'path' => $path,
                'type_path' => 'local'
            ]);
        } else {
            Auth::user()?->image()->update([
                'path' => $path,
                'type_path' => 'local'
            ]);
        }


        return response()->json([
            'status' => 'successfully'
        ]);
    }

    private function deleteImageModelInStorage (): void
    {

        if (!Auth::user()?->image) {
            return;
        }

        $nameFileStorage = ManageImagesStorage::getPathForDeleteImageModel(Auth::user(), "/");

        ManageImagesStorage::deleteImageStorage($nameFileStorage);

        $nameFileStorage = ManageImagesStorage::getPathForDeleteImageModel(Auth::user(), "\\");

        ManageImagesStorage::deleteImageStorage($nameFileStorage);
    }
}
