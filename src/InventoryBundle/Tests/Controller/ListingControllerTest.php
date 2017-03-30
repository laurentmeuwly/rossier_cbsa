<?php

namespace InventoryBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ListingControllerTest extends WebTestCase
{
    public function testPrintbarcode()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/printLivre');
    }

    public function testPrintsiteresume()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'printRapportChantier');
    }

}
