<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    private $productRepository;
    private $categoryRepository;
    private $productService;
    private $serializer;
    private $em;

    public function __construct(EntityManagerInterface $em,
        ProductRepository $productRepository,
        ProductService $productService,
        CategoryRepository $categoryRepository,
        SerializerInterface $serializer) {

        $this->productRepository = $productRepository;
        $this->productService = $productService;
        $this->categoryRepository = $categoryRepository;
        $this->serializer = $serializer;
        $this->em = $em;

    }

    /**
     * @Route("/product", name="product", methods={"GET"} )
     */
    public function getProduct(Request $request)
    {

        $page = $request->get('page', 1);
        $product = $this->productRepository->findAllProducts($page);
        return new JsonResponse([
            'success' => true,
            'data' => $this->serializer->normalize(
                $product,
                'json', ['groups' => ['show-product']]),
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/product/featured", name="product_featured" , methods={"GET"} )
     */
    public function getProductFeatured(Request $request)
    {

        $page = $request->get('page', 1);
        $currency = $request->get('currency', '');
        $featured = 1;

        if ($currency == '' or $currency == null) {
            $tasa = 1;
        } else {

            if (strtolower($currency) != 'usd' and strtolower($currency) != 'eur') {
                throw new ConflictHttpException('parameter currency only accept values EUR or USD');
            }

            $arrayTasas = $this->productService->getTasa($currency);
            $tasa = $arrayTasas[$currency];

        }
        $product = $this->productService->getProductFeatured($featured, $page, $tasa, $currency);

        return new JsonResponse([
            'success' => true,
            'data' => $this->serializer->normalize(
                $product,
                'json', ['groups' => ['show-product']]),
            ], Response::HTTP_OK);


        
    }

    /**
     * @Route("/product", name="product_created" , methods={"POST"} )
     */
    public function createProduct(Request $request)
    {

        try {
            $requestData = json_decode($request->getContent(), true);
            $page = $request->get('page', 1);
            $product = new Product;
            $product = $this->productService->persistProduct($product, $requestData);

            return new JsonResponse(['success' => true], Response::HTTP_OK);
        } catch (\Exception $e) {
            throw new ConflictHttpException($e->getMessage());
        }
    }

    /**
     * @Route("/product", name="product_update",  methods={"PUT"} )
     */
    public function updateProduct(Request $request)
    {
        try {
            $requestData = json_decode($request->getContent(), true);

            if ($requestData['id'] == null or $requestData['id'] == '') {
                throw new ConflictHttpException('error, Id is empty');
            }

            $page = $request->get('page', 1);
            $product = $this->productRepository->find($requestData['id']);

            if (!$product) {
                throw new ConflictHttpException('product not found');
            }

            $product = $this->productService->persistProduct($product, $requestData);

            return new JsonResponse(['success' => true], Response::HTTP_OK);

        } catch (\Exception $e) {
            throw new ConflictHttpException($e->getMessage());
        }

    }

}
