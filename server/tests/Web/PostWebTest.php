<?php namespace Tests\Web;

use Tests\TestCase;

/**
 * @package Tests
 */
class PostWebTest extends TestCase
{
    const SUB_URL = '/posts';

    /**
     * Test read page.
     */
    public function testRead(): void
    {
        $postId = 3;
        $response = $this->get(self::SUB_URL . "/$postId");

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Odio ut ut', (string)$response->getBody());
    }
}
