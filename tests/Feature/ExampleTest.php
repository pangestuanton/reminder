<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_home_redirects_to_login_for_guest(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }
}
