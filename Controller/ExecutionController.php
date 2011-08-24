<?php
namespace Midgard\MidcomCompatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;

class ExecutionController extends Controller
{
    public function execAction($bundle, $file)
    {
        $path = "";
        if ($bundle == 'midcom') {
            $path = $this->container->getParameter('midgard.midcomcompat.root') . "/midcom/exec";
        } else {
            try {
                $bundle = $this->container->get('kernel')->getBundle($bundle);
                $path = $bundle->getPath() . "/exec";
            } catch (\InvalidArgumentException $e) {
                throw new NotFoundHttpException("Bundle {$bundle} is not installed");
            }
        }

        $filePath = "{$path}/{$file}";
        if (!file_exists($filePath)) {
            throw new NotFoundHttpException("Bundle {$bundle} does not have script {$file} {$filePath}");
        }

        $_MIDCOM->setRequest($this->container->get('request'));

        ob_start();
        include($filePath);
        $content = ob_get_clean();
        return new Response($content);
    }
}
