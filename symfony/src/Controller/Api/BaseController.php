<?php

namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractFOSRestController
{
    /**
     * @param mixed|null $content
     * @param int $code
     * @return Response
     */
    protected function createResponse($content = null, int $code = 200)
    {
        $response = new Response();
        $response->headers->add(['Content-Type' => 'application/json']);
        $response->setStatusCode($code);
        $response->setContent($content);

        return $response;
    }

    /**
     * @return Response
     */
    protected function returnCreatedItem()
    {
        return $this->createResponse(null, Response::HTTP_CREATED);
    }

    /**
     * @param FormInterface $form
     * @return Response
     */
    protected function returnFormErrors(FormInterface $form)
    {
        $errorsIterator = $form->getErrors(true);
        $errors = [];
        while ($error = $errorsIterator->current()) {
            $errors[$error->getOrigin()->getName()] = $error->getMessage();
            $errorsIterator->next();
        }
        return $this->createResponse(json_encode($errors), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @param array $list
     * @return Response
     */
    protected function returnList(array $list)
    {
        return $this->createResponse()->setContent($this->prepareContent($list));
    }

    /**
     * @param object|null $item
     * @return Response
     */
    protected function returnItem($item = null)
    {
        return View::create($item);
    }

    /**
     * @param $content
     * @return false|string
     */
    private function prepareContent($content)
    {
        if (is_array($content)) {
            return json_encode($content);
        }

        return $content;
    }
}
