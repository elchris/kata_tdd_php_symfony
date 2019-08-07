<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains(
            '<h1><span>Welcome to</span> Symfony 4.3.3</h1>',
            $client->getResponse()->getContent()
        );
    }
}
