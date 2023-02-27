<?php

namespace App\Tests\Validations;

use App\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationPublishedAtFormationsTest extends KernelTestCase
{
    public function getFormation(): Formation
    {
        return (new Formation())
            ->setTitle("Formation test");
    }

    public function assertErrors(Formation $formation, int $nbErreursAttendues, string $message=""){
        self::bootKernel();
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $error = $validator->validate($formation);
        $this->assertCount($nbErreursAttendues, $error, $message);
    }


    public function testValidDateFormation(){
        $dateTimeTest = (new \DateTime());

        $this->assertErrors($this->getFormation()->setPublishedAt($dateTimeTest), 0, "Aujourd'hui devrait réussir");
        $this->assertErrors($this->getFormation()->setPublishedAt((new \DateTime())->sub(new \DateInterval("P1D"))), 0, "Hier devrait réussir");

    }

    public function testNotValidDateFormation(){
        $dateTimeTest = (new \DateTime())->add(new \DateInterval("P1D"));

        $this->assertErrors($this->getFormation()->setPublishedAt($dateTimeTest), 1, "Demain devrait échouer");
    }
}