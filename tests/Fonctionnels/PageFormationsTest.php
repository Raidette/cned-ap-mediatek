<?php

namespace App\Tests\Fonctionnels;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageFormationsTest extends WebTestCase
{

    // SORTING
    public function testOrderNameDesc(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/title/DESC');

        $this->assertResponseIsSuccessful();
    
        $result = $crawler->filterXPath("//tbody/tr[1]/td/h5")->text();

        dump($result);

        $this->assertSame("Test de formation 3",$result);
    }

    public function testOrderNameAsc(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/title/ASC');

        $this->assertResponseIsSuccessful();
    
        $result = $crawler->filterXPath("//tbody/tr[1]/td/h5")->text();

        dump($result);

        $this->assertSame("Test de formation",$result);
    }

    public function testOrderPlaylistAsc(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/name/ASC/playlist');

        $this->assertResponseIsSuccessful();
    
        $result = $crawler->filterXPath("//tbody/tr[1]/td/h5")->text();

        dump($result);

        $this->assertSame("Test de formation",$result);
    }

    public function testOrderPlaylistDesc(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/name/DESC/playlist');

        $this->assertResponseIsSuccessful();
    
        $result = $crawler->filterXPath("//tbody/tr[1]/td/h5")->text();

        dump($result);

        $this->assertSame("Test de formation 2",$result);
    }

    public function testOrderDateAsc(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/publishedAt/ASC');

        $this->assertResponseIsSuccessful();
    
        $result = $crawler->filterXPath("//tbody/tr[1]/td/h5")->text();

        dump($result);

        $this->assertSame("Test de formation",$result);
    }

    public function testOrderDateDesc(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/publishedAt/DESC');

        $this->assertResponseIsSuccessful();
    
        $result = $crawler->filterXPath("//tbody/tr[1]/td/h5")->text();

        dump($result);

        $this->assertSame("Test de formation 2",$result);
    }


    // SEARCH

    public function testSearchName(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/formations/recherche/title',["recherche" => "2"]);

        $this->assertResponseIsSuccessful();
    
        $result = $crawler->filterXPath("//tbody/tr[1]/td/h5")->text();
        $count = $crawler->filterXPath("//tbody/tr")->count();

        dump($count);

        $this->assertSame("Test de formation 2",$result);
        $this->assertSame(1,$count);
    }

    public function testSearchPlaylist(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/formations/recherche/name/playlist',["recherche" => "2"]);

        $this->assertResponseIsSuccessful();
    
        $result = $crawler->filterXPath("//tbody/tr[1]/td/h5")->text();
        $count = $crawler->filterXPath("//tbody/tr")->count();

        dump($count);

        $this->assertSame("Test de formation 2",$result);
        $this->assertSame(1,$count);
    }

    public function testSearchCategorie(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/formations/recherche/id/categories',["recherche" => "1"]);

        $this->assertResponseIsSuccessful();
    
        $result = $crawler->filterXPath("//tbody/tr[1]/td/h5")->text();
        $count = $crawler->filterXPath("//tbody/tr")->count();

        dump($count);

        $this->assertSame("Test de formation",$result);
        $this->assertSame(1,$count);
    }





    // LINKS

    public function testLinkFormation()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations');

        $this->assertResponseIsSuccessful();

        $a = $crawler->filterXPath("//tbody/tr[1]/td/a")->link();
        $title = $crawler->filterXPath("//tbody/tr[1]/td/h5")->text();

        $crawler = $client->click($a);

        $this->assertResponseIsSuccessful();

        $title2 = $crawler->filterXPath("//h4")->text();

        dump($title2);

        $this->assertSame($title,$title2);
    }
}
