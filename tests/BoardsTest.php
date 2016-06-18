<?php namespace Tests;

use App\Schemes\BoardSchema as Schema;
use DateTime;

/**
 * @package Tests
 */
class BoardsTest extends TestCase
{
    /** API URI */
    const API_URI = '/api/v1/boards';

    /**
     * Test index.
     */
    public function testHasTopLevelMeta()
    {
        $response = $this->get(self::API_URI);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resource = json_decode((string)$response->getBody()));
        $this->assertNotEmpty($resource->meta);
    }

    /**
     * Test `created_at` and `updated_at` cannot be set from input.
     */
    public function testCannotSetModificationDatesOnCreate()
    {
        $text = 'Some title';
        $json = <<<EOT
        {
            "data" : {
                "type"       : "boards",
                "id"         : null,
                "attributes" : {
                    "title" : "$text",
                    "created-at": "2000-01-01 01:02:03",
                    "updated-at": "2001-02-03 03:02:01"
                }
            }
        }
EOT;
        $response = $this->postJson(self::API_URI, $json);
        $this->assertEquals(201, $response->getStatusCode());

        $this->assertNotNull($resource = json_decode((string)$response->getBody()));
        $this->assertNotEmpty($resource->data);
        $this->assertEquals(Schema::TYPE, $resource->data->type);
        $this->assertEquals($text, $resource->data->attributes->{Schema::ATTR_TITLE});
        $this->assertNotEmpty($index = $resource->data->id);
        $this->assertNotEmpty($createdAtString = $resource->data->attributes->{Schema::ATTR_CREATED_AT});
        $createdAt = DateTime::createFromFormat('Y-m-d\TH:i:sO', $createdAtString);
        $this->assertLessThanOrEqual(1, (new DateTime())->diff($createdAt)->days);
        $this->assertEmpty($resource->data->attributes->{Schema::ATTR_UPDATED_AT});

        // check resource created (read)
        $response = $this->get(self::API_URI . "/$index");
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($resource->data->attributes->{Schema::ATTR_CREATED_AT});
        $this->assertEmpty($resource->data->attributes->{Schema::ATTR_UPDATED_AT});

        // update
        $text = 'Some new title';
        $json = <<<EOT
        {
            "data" : {
                "type"       : "boards",
                "id"         : $index,
                "attributes" : {
                    "title" : "$text",
                    "created-at": "2000-01-01 01:02:03",
                    "updated-at": "2001-02-03 03:02:01"
                }
            }
        }
EOT;
        $response = $this->patchJson(self::API_URI . '/' . $index, $json);
        $this->assertEquals(200, $response->getStatusCode());
        $body = (string)$response->getBody();
        $this->assertNotNull($resource = json_decode($body));
        $this->assertNotEmpty($resource->data->attributes->{Schema::ATTR_CREATED_AT});
        $this->assertEquals(
            $createdAt,
            DateTime::createFromFormat('Y-m-d\TH:i:sO', $resource->data->attributes->{Schema::ATTR_CREATED_AT})
        );
        $this->assertNotEmpty($updatedAtString = $resource->data->attributes->{Schema::ATTR_UPDATED_AT});
        $updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $updatedAtString);
        $this->assertLessThanOrEqual(1, (new DateTime())->diff($updatedAt)->days);

        // delete
        $response = $this->delete(self::API_URI . '/' . $index);
        $this->assertEquals(204, $response->getStatusCode());

        // check resource deleted (read)
        $response = $this->get(self::API_URI . "/$index");
        $this->assertEquals(404, $response->getStatusCode());
    }
}
