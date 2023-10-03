<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfileFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $profile = new Profile();
        $profile->setRs('Facebook');
        $profile->setUrl('https://www.facebook.com/amadou.bass.12');

        $profile1 = new Profile();
        $profile1->setRs('Instagram');
        $profile1->setUrl('https://www.instagram.com/bass.amadou');

        $profile2 = new Profile();
        $profile2->setRs('GITHUB');
        $profile2->setUrl(' https://github.com/ghostcoderbass');


        $manager->persist($profile);
        $manager->persist($profile1);
        $manager->persist($profile2);

        $manager->flush();
    }
}
