<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $this->loadMainCategories($manager);
    }

    private function getMainCategoriesData()
    {
        return [
            'Electronics',
            'Toys',
            'Books',
            'Movies'
        ];
    }

    private function loadMainCategories($manager)
    {
        foreach ($this->getMainCategoriesData() as $name){
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
