<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\TestCase;


class SendEmailTest extends TestCase
{
    /** @test */
    public function can_send_a_mail(): void
    {
        Mail::fake();
    }


}