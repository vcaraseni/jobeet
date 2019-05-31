<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\NotificationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_USER')")
 * @Route("/notification")
 */
class NotificationController extends AbstractController
{
    /** @var NotificationRepository */
    private $notificationRepository;

    /**
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @Route("/unread-count", name="notification_unread")
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function unreadCount(): JsonResponse
    {
        dd($this->notificationRepository->findUnseenByUser($this->getUser()));
        return new JsonResponse([
            'count' => $this->notificationRepository->findUnseenByUser($this->getUser())
        ]);
    }


}