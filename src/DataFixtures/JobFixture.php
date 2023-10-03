<?php

namespace App\DataFixtures;

use App\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JobFixture extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $data = [
            "Data Scientist",
            "Statisticien",
            "Analyse cyber-sécurité",
            "Médecin ORL",
            "Echographiste",
            "Mathématicien",
            "Ingenieur logiciel",
            "Analyste informatique",
            "Pathologiste du discours / langage",
            "Dirécteur des Ressources Humaines",
            "Hygiéniste dentaire"
        ];

        for ($i = 0; $i < count($data); $i++){
            $jobs = new Job();
            $jobs->setDesignation($data[$i]);
            $manager->persist($jobs);

        }

        $manager->flush();
    }
}
