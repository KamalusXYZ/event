<?php

namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PageControllerTest extends WebTestCase
{
    public function testHomePage(): void
    {
        $client = static::createClient();
        $client->request('GET','/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }
    public function testRestrictedAdminEvent(): void
    {
        $client = static::createClient();
        $client->request('GET','/admin/event/new');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

    }
//    public function testRestrictedAdminPlace(): void
//    {
//        $client = static::createClient();
//        $client->request('GET','/place/new');
//        $this->assertResponseStatusCodeSame(200);
//
//    }



}
