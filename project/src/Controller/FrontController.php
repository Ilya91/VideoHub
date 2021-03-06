<?php

namespace App\Controller;

use App\Controller\Traits\Likes;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Video;
use App\Form\UserType;
use App\Repository\VideoRepository;
use App\Utils\CategoryTreeFrontPage;
use App\Utils\Interfaces\CacheInterface;
use App\Utils\VideoForNoValidSubscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class FrontController extends AbstractController
{
    use Likes;
    /**
     * @Route("/", name="main_page")
     */
    public function index()
    {
        return $this->render('front/index.html.twig');
    }

    /**
     * @Route("/videolist/{categoryname}/{id}/{page}", defaults={"page": "1"}, name="video_list")
     * @param $id
     * @param $page
     * @param CategoryTreeFrontPage $categories
     * @param Request $request
     * @param VideoForNoValidSubscription $video_no_members
     * @param CacheInterface $cache
     * @return Response
     */
    public function videoList(
        $id,
        $page,
        CategoryTreeFrontPage $categories,
        Request $request,
        VideoForNoValidSubscription $video_no_members,
        CacheInterface $cache )
    {
        $cache = $cache->cache;
        $video_list = $cache->getItem('video_list'.$id.$page.$request->get('sortby'));
        // $video_list->tag(['video_list']);
        $video_list->expiresAfter(60);


        if(!$video_list->isHit())
        {
            $ids = $categories->getChildIds($id);
            array_push($ids, $id);

            $videos = $this->getDoctrine()
                ->getRepository(Video::class)
                ->findByChildIds($ids ,$page, $request->get('sortby'));

            $categories->getCategoryListAndParent($id);
            $response = $this->render('front/videolist.html.twig',[
                'subcategories' => $categories,
                'videos'=>$videos,
                'video_no_members' => $video_no_members->check()
            ]);

            $video_list->set($response);
            $cache->save($video_list);
        }

        return $video_list->get();
    }

    /**
     * @Route("/video-details/{video}", name="video_details")
     * @param VideoRepository $repo
     * @param $video
     * @return Response
     */
    public function videoDetails(VideoRepository $repo, $video): Response
    {
        return $this->render('front/video_details.html.twig',
            [
                'video'=>$repo->videoDetails($video),
            ]);
    }

    /**
     * @Route("/new-comment/{video}", methods={"POST"}, name="new_comment")
     * @param Video $video
     * @param Request $request
     * @return RedirectResponse
     */
    public function newComment(Video $video, Request $request ): RedirectResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        if ( !empty( trim($request->request->get('comment')) ) )
        {

            // $video = $this->getDoctrine()->getRepository(Video::class)->find($video_id);

            $comment = new Comment();
            $comment->setContent($request->request->get('comment'));
            $comment->setUser($this->getUser());
            $comment->setVideo($video);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
        }

        return $this->redirectToRoute('video_details',['video'=>$video->getId()]);
    }

    /**
     * @Route("/search-results/{page}", methods={"GET"}, defaults={"page": "1"}, name="search_results")
     */
    public function searchResults($page, Request $request)
    {
        $videos = null;
        $query = null;

        if($query = $request->get('query'))
        {
            $videos = $this->getDoctrine()
                ->getRepository(Video::class)
                ->findByTitle($query, $page, $request->get('sortby'));

            if(!$videos->getItems()) $videos = null;
        }

        return $this->render('front/search_results.html.twig',[
            'videos' => $videos,
            'query' => $query,
        ]);
    }


    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @return Response
     */
    public function register(UserPasswordEncoderInterface $password_encoder, Request $request)
    {
        $user = new User;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $user->setName($request->request->get('user')['name']);
            $user->setLastName($request->request->get('user')['last_name']);
            $user->setEmail($request->request->get('user')['email']);
            $password = $password_encoder->encodePassword($user, $request->request->get('user')['password']['first']);
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->loginUserAutomatically($user, $password);

            return $this->redirectToRoute('admin_main_page');
        }
        return $this->render('front/register.html.twig',['form'=>$form->createView()]);
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $helper
     * @return Response
     */
    public function login(AuthenticationUtils $helper)
    {
        return $this->render('front/login.html.twig', [
            'error' => $helper->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout() : void
    {
        throw new \Exception('This should never be reached!');
    }


    /**
     * @return Response
     */
    public function mainCategories(): Response
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findBy(['parent'=>null], ['name'=>'ASC']);

        return $this->render('front/_main_categories.html.twig',[
            'categories'=>$categories
        ]);
    }

    /**
     * @param $user
     * @param $password
     */
    private function loginUserAutomatically($user, $password): void
    {
        $token = new UsernamePasswordToken(
            $user,
            $password,
            'main', // security.yaml
            $user->getRoles()
        );
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main',serialize($token));
    }

    /**
     * @Route("/video/{video}/like", name="like_video", methods={"POST"})
     * @Route("/video/{video}/dislike", name="dislike_video", methods={"POST"})
     * @Route("/video/{video}/unlike", name="undo_like_video", methods={"POST"})
     * @Route("/video/{video}/undodislike", name="undo_dislike_video", methods={"POST"})
     * @param Video $video
     * @param Request $request
     * @return JsonResponse
     */
    public function toggleLikesAjax(Video $video, Request $request): JsonResponse
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        switch($request->get('_route'))
        {
            case 'like_video':
                $result = $this->likeVideo($video);
                break;

            case 'dislike_video':
                $result = $this->dislikeVideo($video);
                break;

            case 'undo_like_video':
                $result = $this->undoLikeVideo($video);
                break;

            case 'undo_dislike_video':
                $result = $this->undoDislikeVideo($video);
                break;
        }

        return $this->json(['action' => $result,'id'=>$video->getId()]);
    }

}
