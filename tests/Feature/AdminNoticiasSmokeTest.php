<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNoticiasSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_index_returns_successfully_when_database_is_empty(): void
    {
        $response = $this->get('/noticias');

        $response->assertStatus(200);
    }
}
