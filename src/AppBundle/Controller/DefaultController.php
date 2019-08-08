<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render(
            'default/index.html.twig', [
                'base_dir' => 'derp'
                //dirname($this->getParameter('kernel.root_dir')) . '' .DIRECTORY_SEPARATOR
            ]
        );
    }
}
