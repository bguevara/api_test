<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;

class CategoryFixtures extends Fixture
{

    public static function getReferenceKey($i) {
        return sprintf('product_category_%s ',$i);
    }

    public function load(ObjectManager $manager)
    {
         for ($i=0; $i<=5;$i++) { 
             $category = new Category();
             $category->setName('Category'.$i);
             $category->setDescription('the category is : '.$i);
             $category->setActive(1);
             $manager->persist($category);
             $this->addReference(self::getReferenceKey($i),$category);
        }
        $manager->flush();
    }
}
