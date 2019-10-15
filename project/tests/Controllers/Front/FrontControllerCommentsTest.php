<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\Rollback;

class FrontControllerCommentsTest extends WebTestCase
{
    use RoleAdmin;

    public function testNotLoggedInUser()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/video-details/16');

        $form = $crawler->selectButton('Add')->form([
            'comment' => 'Test comment'
        ]);
        $client->submit($form);

        $this->assertContains('Please sign in', $client->getResponse()->getContent());
    }


    /**
     *
     */
    public function testNewCommentAndNumberOfComments()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/video-details/16');

        $form = $crawler->selectButton('Add')->form([
            'comment' => 'Test comment',
        ]);
        $client->submit($form);

        $this->assertContains('Test comment', $client->getResponse()->getContent());

        $crawler = $client->request('GET', '/videolist/toys/2');
        $this->assertSame('Comments (1)', $crawler->filter('a.ml-1')->text());

    }
}

