<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/micro-post")
 */
class MicroPostController
{
    /** @var \Twig_Environment */
    private $twig;

    /** @var MicroPostRepository  */
    private $microPostRepository;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var EntityManagerInterface */
    private $em;

    /** @var RouterInterface */
    private $router;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /**
     * @param \Twig_Environment $twig
     * @param MicroPostRepository $microPostRepository
     * @param FormFactoryInterface $factory
     * @param EntityManagerInterface $em
     * @param RouterInterface $router
     * @param FlashBagInterface $flashBag
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        \Twig_Environment $twig,
        MicroPostRepository $microPostRepository,
        FormFactoryInterface $factory,
        EntityManagerInterface $em,
        RouterInterface $router,
        FlashBagInterface $flashBag,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->twig = $twig;
        $this->microPostRepository = $microPostRepository;
        $this->formFactory = $factory;
        $this->em = $em;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/", name="micro_post_index")
     *
     * @param TokenStorageInterface $tokenStorage
     *
     * @param UserRepository $userRepository
     * @return Response
     *
     * @throws \Exception
     */
    public function index(TokenStorageInterface $tokenStorage, UserRepository $userRepository): Response
    {
        /** @var User $user */
        $authenticatedUser = $tokenStorage->getToken()->getUser();
        $usersToFollow = [];

        $posts = null;
        if ($authenticatedUser instanceof User) {
            $posts = $this->microPostRepository->findAllByUsers($authenticatedUser->getFollowing());

            $usersToFollow = count($posts) === 0 ?
                $userRepository->findAllWithMoreThan5PostsExceptUser($authenticatedUser) : [];
        } else {
            $posts = $this->microPostRepository->findBy([],['time' => 'DESC']);
        }


        $response = $this->twig->render('micro-post/index.html.twig', [
            'posts' => $posts,
            'usersToFollow' => $usersToFollow,
        ]);

        return new Response($response);
    }

    /**
     * @Route("/edit/{id}", name="micro_post_edit")
     * @Security("is_granted('edit', microPost)", message="Access denied.")
     *
     * @param MicroPost $microPost
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function edit(MicroPost $microPost, Request $request): Response
    {
        $form = $this->formFactory->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return new RedirectResponse(
                $this->router->generate('micro_post_index')
            );
        }

        return new Response(
            $this->twig->render('micro-post/add.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }

    /**
     * @Route("/delete/{id}", name="micro_post_delete")
     * @Security("is_granted('delete', microPost)", message="Access denied.")
     *
     * @param MicroPost $microPost
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function delete(MicroPost $microPost): Response
    {
        $this->em->remove(
            $this->microPostRepository->find($microPost)
        );
        $this->em->flush();

        $this->flashBag->add('notice', 'Message was deleted');

        return new RedirectResponse(
            $this->router->generate('micro_post_index')
        );
    }

    /**
     * @Route("/add", name="micro_post_add")
     * @Security("is_granted('ROLE_USER')")
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function add(Request $request): Response
    {
        $user = $this->tokenStorage->getToken()->getUser();

        $microPost = new MicroPost();
        $microPost->setUser($user);

        $form = $this->formFactory->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($microPost);
            $this->em->flush();

            return new RedirectResponse($this->router->generate('micro_post_index'));
        }

        return new Response(
            $this->twig->render('micro-post/add.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }

    /**
     * @Route("/user/{username}", name="micro_post_user")
     *
     * @param User $userWithPosts
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function userPosts(User $userWithPosts): Response
    {
        $response = $this->twig->render('micro-post/user-posts.html.twig', [
            'posts' => $userWithPosts->getPosts(),
            'user' => $userWithPosts,
        ]);

        return new Response($response);
    }

    /**
     * @Route("/{id}", name="micro_post_show")
     *
     * @param MicroPost $microPost
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function show(MicroPost $microPost): Response
    {
        return new Response($this->twig->render(
            'micro-post/post.html.twig',
            [
                'post' => $microPost
            ]
        ));
    }
}