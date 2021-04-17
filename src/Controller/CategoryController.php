<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

use App\Service\CategoryService;


class CategoryController extends AbstractController
{
    private $categoryRepository;
    private $categoryService;
    private $serializer;
    private $em;

    public function __construct(EntityManagerInterface $em,
                                CategoryRepository $categoryRepository,
                                CategoryService $categoryService,
                                SerializerInterface $serializer) {
                                    
        $this->categoryRepository = $categoryRepository;
        $this->categoryService = $categoryService;
        $this->serializer = $serializer;
        $this->em = $em;

    }

    /**
     * @Route("/category", name="category", methods={"GET"} )
     */
    public function getCategory(Request $request)
    {

        $page = $request->get('page', 1);
        $category = $this->categoryRepository->findAllCategory();
        return new JsonResponse([
            'success' => true,
            'data' => $this->serializer->normalize(
                $category,
                'json', ['groups' => ['show-category']]),
        ], Response::HTTP_OK);
    }

    
    /**
     * @Route("/category", name="category_created" , methods={"POST"} )
     */
    public function createCategory(Request $request)
    {

        try {
            $requestData = json_decode($request->getContent(), true);
            $page = $request->get('page', 1);
            $category = new Category;
            $category=$this->categoryService->persistCategory($category,$requestData);
            return new JsonResponse(['success' => true], Response::HTTP_OK);

        } catch (\Exception $e) {
            throw new ConflictHttpException($e->getMessage());
        }
    }

    /**
     * @Route("/category", name="category_update",  methods={"PUT"} )
     */
    public function updateCategory(Request $request)
    {
        try {
            $requestData = json_decode($request->getContent(), true);
            $page = $request->get('page', 1);

            if ($requestData['id']==null or $requestData['id']=='') {
                throw new ConflictHttpException('error, Id is empty');
            }
            $category = $this->categoryRepository->find($requestData['id']);
            if (!$category) {
                throw new ConflictHttpException('category not found');
            }

            $category=$this->categoryService->persistCategory($category,$requestData);

            return new JsonResponse(['success' => true], Response::HTTP_OK);

        } catch (\Exception $e) {
            throw new ConflictHttpException($e->getMessage());
        }

    }


    /**
     * @Route("/category", name="category_delete",  methods={"DELETE"} )
     */
    public function deleteCategory(Request $request)
    {
        try {
            $requestData = json_decode($request->getContent(), true);
            $page = $request->get('page', 1);

            if ($requestData['id']==null or $requestData['id']=='') {
                throw new ConflictHttpException('error, Id is empty');
            }

            $category = $this->categoryRepository->find($requestData['id']);

            if (!$category) {
                throw new ConflictHttpException('category not found');
            }
            $this->em->remove($category);
            $this->em->flush();
            return new JsonResponse(['success' => true], Response::HTTP_OK);

        } catch (\Exception $e) {
            throw new ConflictHttpException($e->getMessage());
        }

    }





}
