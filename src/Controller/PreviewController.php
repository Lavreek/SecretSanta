<?php

namespace App\Controller;

use App\Form\GameCreateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;

class PreviewController extends AbstractController
{
    #[Route('/', name: 'app_root')]
    public function previewRoot(): Response
    {
        return $this->render('preview/root.html.twig');
    }
}
