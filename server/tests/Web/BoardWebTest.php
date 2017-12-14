<?php namespace Tests\Web;

use App\Json\Schemes\BoardScheme;
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
        $this->assertContains('Regions', (string)$response->getBody());
        $this->assertLessThan(0.5, $time, 'Our home page has become sloppy.');
    }

    /**
     * Test index page.
     */
    public function testIndexWithFilters(): void
    {
        $queryParams = [
            'filter' => [
                BoardScheme::ATTR_TITLE => [
                    'not-like' => '%Regions%',
                ],
                BoardScheme::ATTR_CREATED_AT => [
                    'less-than' => '2100-02-03T04:05:06+0000',
                ],
            ],
        ];

        // execution time measurement example
        $response = $this->get(self::ROOT_SUB_URL, $queryParams);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotContains('Regions', (string)$response->getBody());
    }

    /**
     * Test read board.
     */
    public function testRead(): void
    {
        $queryParams = [
            'page' => [
                'offset' => '5',
                'limit'  => '10',
            ],
        ];

        $boardId  = 3;
        $response = $this->get(self::BOARD_SUB_URL . $boardId, $queryParams);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Prev', (string)$response->getBody());
        $this->assertContains('Next', (string)$response->getBody());
    }
}
