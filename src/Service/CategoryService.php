<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
{
    private $em;
    private $categoryRepository;
    public function __construct(EntityManagerInterface  $em,
                                CategoryRepository $categoryRepository)
    {
        $this->em = $em;
        $this->categoryRepository = $categoryRepository;
    }

        
    public function persistCategory($category, $requestData)
    {
        $category->setName($requestData['name']);
        $category->setDescription($requestData['description']);
        $category->setActive($requestData['active']);
        $this->em->persist($category);
        $this->em->flush();
        return $category;
    }

    

  
}
