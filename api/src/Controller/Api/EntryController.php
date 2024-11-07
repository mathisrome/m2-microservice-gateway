<?php

namespace App\Controller\Api;

use App\Entity\Service;
use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EntryController extends AbstractController
{
    #[Route('/{namespace}/{version}/{resource}', name: 'entry', requirements: ['resource' => '.+', 'version' => '[0-9]{1,2}\.[0-9]{1}'], methods: ['POST', 'GET', 'DELETE', 'PUT', 'PATCH'])]
    public function index(
        Request                $request,
        string                 $namespace,
        string                 $version,
        string                 $resource,
        EntityManagerInterface $em,
        HttpClientInterface    $client,
    ): Response
    {
        $service = $em->getRepository(Service::class)->findOneBy([
            "namespace" => $namespace,
            "version" => $version,
        ]);

        if (empty($service)) {
            throw $this->createNotFoundException('Unknown service');
        }

        if ($service->getVersion() > $version) {
            throw new \Exception("Unsupported version (" . $service->getVersion() . " minimum)", 400);
        }

        $requestData = [
            "headers" => [
                "content-type" => $request->headers->get("content-type"),
            ],
            "query" => $request->query->all(),
        ];

        if ($request->headers->has("content-type") && str_contains($request->headers->get("content-type"), "application/json")) {
            $requestData["body"] = $request->getContent();
        } else if ($request->headers->has("content-type") && str_contains($request->headers->get("content-type"), "multipart/form-data")) {
            $body = [];

            foreach ($request->request->all() as $key => $item) {
                $body[$key] =  $item;
            }

            foreach ($request->files->all() as $key => $item) {
                $body[$key] =  fopen($item->getRealPath(), 'r');
            }

            $requestData["body"] = $body;
        } else {
            $requestData["body"] = $request->request->all();
        }

        $req = $client->request(
            $request->getMethod(),
            $service->getUrl() . '/' . $resource,
            $requestData
        );


        // On retourne la réponse du service distant
        $response = new Response();
        $response->setStatusCode($req->getStatusCode());
        $response->setContent($req->getContent(false));
        if (array_key_exists("content-type", $req->getHeaders(false))) {
            $response->headers->set("content-type", $req->getHeaders(false)["content-type"][0]);
        }

        return $response;
    }
}
