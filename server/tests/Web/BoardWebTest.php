<?php namespace Tests\Web;

use Tests\TestCase;

/**
 * @package Tests
 */
class BoardWebTest extends TestCase
{
    const ROOT_SUB_URL = '/';

    const BOARD_SUB_URL = '/boards/';

    /**
     * Test index page.
     */
    public function testIndex(): void
    {
        // execution time measurement example
        $response = $this->measureTime(function () {
            return $this->get(self::ROOT_SUB_URL);
        }, $time);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Unde dolore tempore', (string)$response->getBody());
        $this->assertLessThan(0.5, $time, 'Our home page has become sloppy.');
    }

    /**
     * Test read board.
     */
    public function testRead(): void
    {
        $queryParams = [
            'page' => [
                'skip' => '5',
                'size' => '10',
            ],
        ];

        $boardId  = 3;
        $response = $this->get(self::BOARD_SUB_URL . $boardId, $queryParams);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Prev', (string)$response->getBody());
        $this->assertContains('Next', (string)$response->getBody());
    }
}
