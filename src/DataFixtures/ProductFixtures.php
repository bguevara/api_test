<?php

namespace App\DataFixtures;

use App\DataFixtures\CategoryFixtures; 
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Product;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    
    public function load(ObjectManager $manager)
    {
         for ($i=0; $i<=20;$i++) { 
             $category=$this->getReference(CategoryFixtures::getReferenceKey($i % 5)); 
             $product = new Product();
             $product->setName('product'.$i);
             $product->setPrice(100);
             $product->setCurrency('usd');
             $product->setFeatured('new');
             $product->setcategory($category);
             $manager->persist($product);
        }
        $manager->flush();
    }
  
    public function getDependencies()
    {
        return [
            CategoryFixtures::class
        ];
    } 

}
