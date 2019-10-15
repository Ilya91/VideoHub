<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\RoleUser;

class FrontControllerLikesTest extends WebTestCase
{
    use RoleUser;

    public function testLike()
    {

        $this->client->request('POST', '/video/11/like');
        $crawler = $this->client->request('GET', '/videolist/movies/4');

        $this->assertSame('(3)', $crawler->filter('small.number-of-likes-11')->text());
    }

    public function testDislike()
    {

        $this->client->request('POST', '/video/11/dislike');
        $crawler = $this->client->request('GET', '/videolist/movies/4');

        $this->assertSame('(1)', $crawler->filter('small.number-of-dislikes-11')->text());
    }

    public function testNumberOfLikedVidoes1()
    {
        $this->client->request('POST', '/video/12/like');
        $this->client->request('POST', '/video/12/like');

        $crawler = $this->client->request('GET', '/admin/videos');

        $this->assertEquals(2, $crawler->filter('tr.video')->count());
    }

    public function testNumberOfLikedVidoes2()
    {
        $this->client->request('POST', '/video/1/unlike');
        $this->client->request('POST', '/video/12/unlike');

        $crawler = $this->client->request('GET', '/admin/videos');

        $this->assertEquals(1, $crawler->filter('tr.video')->count());
    }
}

