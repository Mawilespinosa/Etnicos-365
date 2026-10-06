<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * The root URL redirects to the public store.
     */
    public function test_the_root_url_redirects_to_the_store(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/tienda');
    }
}
