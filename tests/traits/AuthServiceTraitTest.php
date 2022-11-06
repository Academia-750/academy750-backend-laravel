<?php

namespace Tests\traits;

use App\Models\User;
use Laravel\Sanctum\Sanctum;

trait AuthServiceTraitTest
{
    public $userAuth;

    public function authenticateUser ($modelRole) {
        $userAuth = User::factory()->create([
            'first_name' => 'Raul Alberto',
            'last_name' => 'Moheno Zavaleta',
        ]);
        $userAuth->assignRole($modelRole);
        Sanctum::actingAs($userAuth);

        $this->userAuth = $userAuth;

        return $userAuth;
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->authenticateUser($this->roleAdmin);
    }
}
