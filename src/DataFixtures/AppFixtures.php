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
        $manager->persist($this->newStatus(0, 'Новый'));
        $manager->persist($this->newStatus(1, 'Новый'));
        $manager->persist($this->newStatus(2, 'Назначено собеседование'));
        $manager->persist($this->newStatus(3, 'Принят'));
        $manager->persist($this->newStatus(4, 'Отказ'));

        $manager->flush();
    }

    public function newStatus(int $i, string $status): Resume
    {
        $resume = new Resume();
        $resume
            ->setProfession("test${i}")
            ->setCity("Липецк")
            ->setPhoto("https://drasler.ru/wp-content/uploads/2019/01/brad_pitt__chb_portret_s_sigaretoy_1400.jpg")
            ->setFIO("test${i}")
            ->setPhone("test${i}")
            ->setEmail("test${i}")
            ->setBirthDate("195${i}")
            ->setSalary("до 50 тыс")
            ->setKeySkills('[{"id":1,"title":"Обучаемость"}]')
            ->setAbout("test${i}")
            ->setStatus($status);
        return $resume;
    }
}
