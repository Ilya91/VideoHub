<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Symfony\Component\Cache\Adapter\RedisAdapter;

class FrontControllerCacheTest extends WebTestCase
{
    use RoleUser;

    public function testCache()
    {

        $client = self::createClient();
        $client->enableProfiler();
        $this->assertTrue(true);

        $client->request('GET', '/videolist/movies/4/3');

        $this->assertGreaterThan(
            4,
            $client->getProfile()->getCollector('db')->getQueryCount()
        );


        $client->enableProfiler();
        $client->request('GET', '/videolist/movies/4/3');

        $this->assertEquals(
            0,
            $client->getProfile()->getCollector('db')->getQueryCount()
        );

    }

}

