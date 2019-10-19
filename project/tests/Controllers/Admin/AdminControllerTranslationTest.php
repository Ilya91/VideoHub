<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class AdminControllerTranslationTest extends WebTestCase
{
    use RoleUser;

    public function testTranslations()
    {

        $this->client->request('GET', '/ru/admin/');

        $this->assertContains( 'Мой профиль', $this->client->getResponse()->getContent() );
        //$this->assertContains( 'lista-video', $this->client->getResponse()->getContent() );
    }
}
