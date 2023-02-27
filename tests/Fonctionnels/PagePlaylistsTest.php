<?php

namespace App\Tests\Fonctionnels;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PagePlaylistsTest extends WebTestCase
{

    // SORTING
    public function testOrderNameDesc(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/name/DESC');

        $this->assertResponseIsSuccessful();
    
        $result = $crawler->filterXPath("//tbody/tr[1]/td/h5")->text();

        $this->assertSame("Playlist test 2",$result);
    }

    public function testOrderNameAsc(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/name/ASC');

        $this->assertResponseIsSuccessful();
    
        $result = $crawler->filterXPath("//tbody/tr[1]/td/h5")->text();

        dump($result);

        $this->assertSame("Playlist test 1",$result);
    }

    public function testOrderTailleAsc(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/nbformations/ASC');

        $this->assertResponseIsSuccessful();
    
        $result = $crawler->filterXPath("//tbody/tr[1]/td/h5")->text();

        dump($result);

        $this->assertSame("Playlist test 2",$result);
    }

    public function testOrderTailleDesc(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/nbformations/DESC');

        $this->assertResponseIsSuccessful();
    
        $result = $crawler->filterXPath("//tbody/tr[1]/td/h5")->text();

        dump($result);

        $this->assertSame("Playlist test 1",$result);

        
    }

    public function testSearchCategorie(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/playlists/recherche/id/categories',["recherche" => "1"]);

        $this->assertResponseIsSuccessful();
    
        $result = $crawler->filterXPath("//tbody/tr[1]/td/h5")->text();
        $count = $crawler->filterXPath("//tbody/tr")->count();

        //dump($count);

        $this->assertSame("Playlist test 1",$result);
        $this->assertSame(1,$count);
    }

    public function testLinkPlaylist()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists');

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
