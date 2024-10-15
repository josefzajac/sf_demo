<?php declare(strict_types=1);

namespace Document\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class RootController extends AbstractController
{
    #[Route('/', name: 'root', methods: ['GET'])]
    public function root(): JsonResponse
    {
        return $this->json(['ok - ' . $this->getParameter('app.company')]);
    }
}
