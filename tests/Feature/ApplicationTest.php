<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApplicationTest extends TestCase
{
    /**
     * A test to check if the server is running with success.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/api');

        $response->assertStatus(200);
    }
}
