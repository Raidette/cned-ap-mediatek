<?php

namespace App\Tests;

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

class DateParutionUnitTest extends TestCase
{
    public function testGetDateParutionString()
    {
        $formation = new Formation();

        $formation->setPublishedAt(\DateTime::createFromFormat(("Y-m-d"),"2023-02-13"));

        $this->assertEquals("13/02/2023",$formation->getPublishedAtString());

    }

}

