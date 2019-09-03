<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Event\UserRegisterEvent;
use App\Form\UserType;
use App\Security\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Request $request
     * @param EventDispatcherInterface $dispatcher
     * @param TokenGenerator $tokenGenerator
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function register(
        UserPasswordEncoderInterface $passwordEncoder,
        Request $request,
        EventDispatcherInterface $dispatcher,
        TokenGenerator $tokenGenerator
    ): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
//        $form = $this->formFactory->create(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setConfirmationToken($tokenGenerator->getRandomSecureToken(25));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $event = new UserRegisterEvent($user);
            $dispatcher->dispatch(UserRegisterEvent::NAME, $event);

            $this->redirect('micro_post_index');
        }

        return $this->render('register/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}