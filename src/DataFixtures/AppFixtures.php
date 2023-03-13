<?php

namespace App\DataFixtures;

use App\Entity\Education;
use App\Entity\Resume;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $array = $this->newStatus();
        foreach ($array as $resume) {
            $manager->persist($resume);
        }
        $manager->flush();
    }

    public function newStatus(): array
    {
        $array = [];
        for ($i = 0; $i < 3; $i++) {
            $resume = new Resume();
            $resume
                ->setProfession("test${i}")
                ->setCity("Липецк")
                ->setPhoto("https://drasler.ru/wp-content/uploads/2019/01/brad_pitt__chb_portret_s_sigaretoy_1400.jpg")
                ->setFIO("test${i}")
                ->setPhone("test${i}")
                ->setEmail("test${i}")
                ->setBirthDate("test${i}")
                ->setSalary("до 50 тыс")
                ->setKeySkills('[{"id":1,"title":"Обучаемость"}]')
                ->setAbout("test${i}")
                ->setStatus("Новый");
            $array[] = $resume;
        }
        return $array;
    }
}
