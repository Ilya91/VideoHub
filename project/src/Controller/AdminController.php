<?php

namespace App\Controller;

use App\Form\CategoryType;
use App\Utils\CategoryTreeAdminOptionList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\CategoryTreeAdminList;
use App\Entity\Category;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_main_page")
     */
    public function index(): Response
    {
        return $this->render('admin/my_profile.html.twig');
    }


    /**
     * @Route("/su/categories", name="categories", methods={"GET", "POST"})
     * @param CategoryTreeAdminList $categories
     * @param Request $request
     * @return Response
     */
    public function categories(CategoryTreeAdminList $categories, Request $request): Response
    {
        $categories->getCategoryList($categories->buildTree());

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $is_invalid = null;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $category->setName($request->request->get('category')['name']);

            $repository = $this->getDoctrine()->getRepository(Category::class);
            $parent = $repository->find($request->request->get('category')['parent']);
            $category->setParent($parent);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('categories');
        }elseif($request->isMethod('post'))
        {
            $is_invalid = ' is-invalid';
        }

        return $this->render('admin/categories.html.twig',[
            'categories'=>$categories->categorylist,
            'form' => $form->createView(),
            'is_invalid'=>$is_invalid
        ]);
    }

    /**
     * @Route("/su/edit-category/{id}", name="edit_category")
     * @param Category $category
     * @return Response
     */
    public function editCategory(Category $category): Response
    {
        return $this->render('admin/edit_category.html.twig', [
            'category' => $category
        ]);
    }

    /**
     * @Route("/su/update-category/{id}", name="update_category")
     * @param Request $request
     * @param Category $category
     * @return RedirectResponse
     */
    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();
        $parent = $em->getRepository(Category::class)->find((int)($request->get('parent')));

        $category->setName($request->get('name'));
        $category->setParent($parent);
        $em->persist($category);
        $em->flush();

        return $this->redirectToRoute('categories');
    }

    /**
     * @Route("/su/delete-category/{id}", name="delete_category")
     * @param Category $category
     * @return RedirectResponse
     */
    public function deleteCategory(Category $category): RedirectResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();
        return $this->redirectToRoute('categories');
    }

    /**
     * @Route("/videos", name="videos")
     */
    public function videos(): Response
    {
        return $this->render('admin/videos.html.twig');
    }

    /**
     * @Route("/su/upload-video", name="upload_video")
     */
    public function uploadVideo(): Response
    {
        return $this->render('admin/upload_video.html.twig');
    }

    /**
     * @Route("/su/users", name="users")
     */
    public function users(): Response
    {
        return $this->render('admin/users.html.twig');
    }

    /**
     * @param CategoryTreeAdminOptionList $categories
     * @param null $editedCategory
     * @return Response
     */
    public function getAllCategories(CategoryTreeAdminOptionList $categories, $editedCategory = null): Response
    {
        $categories->getCategoryList($categories->buildTree());
        return $this->render('admin/_all_categories.html.twig',[
            'categories' => $categories,
            'editedCategory' => $editedCategory
        ]);
    }
}

