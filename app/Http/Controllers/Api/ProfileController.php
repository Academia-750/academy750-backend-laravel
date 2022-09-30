<?php

namespace App\Http\Controllers\Api;

use App\Core\Resources\Profile\Interfaces\ProfileInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller implements ProfileInterface
{
    protected ProfileInterface $profileInterface;

    public function __construct(ProfileInterface $profileInterface)
    {
        $this->profileInterface = $profileInterface;
    }

    public function getDataMyProfile()
    {
        return $this->profileInterface->getDataMyProfile();
    }
}
