<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\MediaTopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class ProductService
{
    private $em;
    private $productRepository;
    private $categoryRepository;
    private $params;

    public function __construct(EntityManagerInterface  $em,
                                ProductRepository $productRepository,
                                CategoryRepository $categoryRepository,
                                ParameterBagInterface $params)
    {
        $this->em = $em;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->params=$params;
    }

    
    public function getProductFeatured($featured,$page=1,$tasa=1,$currency='usd')
    {
        $product = $this->productRepository->findAllProducts($featured, $page,$tasa);
        $productResult=null;
        foreach ($product as $value) {
            $productResult[]["id"]=$value['id'];
            $productResult[]["name"]=$value['name'];
            $productResult[]["featured"]=$value['featured'];
            $productResult[]["category"]=$value['category'];
            $productResult[]["currency"]=$value['currency'];
            $productResult[]["price"]=($value['currency']<>$currency) ? ($value['price'] * $tasa) : $value['price'];
           
        }
        return $productResult;
      
    }

    public function persistProduct($product, $requestData)
    {
        if ($requestData['category'] == '' or $requestData['category'] == null) {
            $category = null;
        } else {
            $category = $this->categoryRepository->find($requestData['category']);
        }

        
        $product->setName($requestData['name']);
        $product->setPrice($requestData['price']);
        $product->setCategory($category);
        $product->setFeatured($requestData['featured']);
        $product->setCurrency($requestData['currency']);
        $this->em->persist($product);
        $this->em->flush();
        return $product;
    }

    public function getTasa()
    {
        $apiKey=$this->params->get('api.key.rate');
        $urlRateBase=$this->params->get('url.rate');

        $url=$urlRateBase."latest?access_key=".$apiKey."&symbols=usd&base=EUR";
	    $json = file_get_contents($url);
        $tasas = json_decode($json, TRUE);
        
        if (in_array("success", $tasas)) {
            $valorEUR=$tasas['rates']['USD'];
            $valorUSD=1/$valorEUR;
        } else {
            throw new ConflictHttpException('Error get rate api');
        }
        
        return ['eur'=>$valorUSD,'usd'=>$valorEUR];
        
    }


  
}
