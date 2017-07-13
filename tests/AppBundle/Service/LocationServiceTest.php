<?php


namespace Tests\AppBundle\Service;

use Tests\AppBundle\AppTestCase;

class LocationServiceTest extends AppTestCase
{
    /*
     * home: 37.773160, -122.432444
     * work: 37.7721718,-122.4310872
     */
    public function testGetOrCreateLocation()
    {
        self::assertEquals(37.773160, $this->home->getLat(), 0.00000001);
        self::assertEquals(-122.432444, $this->home->getLong(), 0.00000001);
    }
}
