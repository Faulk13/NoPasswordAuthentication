<?php

namespace App\Controller;

use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Rémi Marcherat<rmarcherat@prestaconcept.net>
 */
class DefaultController extends Controller
{
    /**
     * @return Response
     */
    public function securedAction()
    {
        return $this->render('secured/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
}
