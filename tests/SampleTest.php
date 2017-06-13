<?php namespace Tests;

/**
 * @package Tests
 */
class SampleTest extends TestCase
{
    /**
     * Test home page.
     */
    public function testHomePage()
    {
        // if you're are working with database the following method would help to prevent changes in the database.
        // Powerful feature 'multi-call' is supported. Multiple calls GET/POST/PUT/PATCH are supported within one test.
        // All such calls would be called within on database transaction which is very convenient for testing.
        //$this->setPreventCommits();

        // if `setPreventCommits` was called then a connection shared within the test could be received with
        //$this->getCapturedConnection();

        $response = $this->get('/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('I\'ve got some Limoncello.', (string)$response->getBody());

        // execution time measurement example
        $response = $this->measureTime(function () {
            return $this->get('/');
        }, $time);

        $this->assertLessThan(0.5, $time, 'Our home page has become sloppy.');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test welcome page.
     */
    public function testWelcomePage()
    {
        $response = $this->get('/welcome');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Fast and flexible PHP framework', (string)$response->getBody());
    }
}
