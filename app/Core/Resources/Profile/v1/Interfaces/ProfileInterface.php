<?php

namespace App\Core\Resources\Profile\v1\Interfaces;

interface ProfileInterface
{
    public function getDataMyProfile ();
    public function updateDataMyProfile ($request);
}
