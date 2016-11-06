<?php namespace Tests;

/**
 * @package Tests
 */
class HomePageTest extends TestCase
{
    /**
     * Test home page.
     */
    public function testHomePage()
    {
        $response = $this->get('/');

        $this->assertEquals(200, $response->getStatusCode());
        $body = (string)$response->getBody();
        $this->assertContains('Fast and flexible micro-framework', $body);

        // execution time measurement example
        $response = $this->measureTime(function () {
            return $this->get('/');
        }, $time);

        $this->assertLessThan(0.15, $time, 'Our home page has become sloppy.');
        $this->assertEquals(200, $response->getStatusCode());
    }
}
