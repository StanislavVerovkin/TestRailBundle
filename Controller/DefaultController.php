<?php

namespace Test\TestrailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TestTestrailBundle:Default:index.html.twig');
    }
}
