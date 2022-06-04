<?php


namespace App\Controller\Manager;


use App\Repository\CommuneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class DepartmentController
 * @package App\Controller\Manager
 * @Route("/manager/commune")
 */
class CommuneController extends AbstractController
{
    /**
     * @param CommuneRepository $communeRepository
     * @param SerializerInterface $serializer
     * @return Response
     * @Route(name="manager_list_communes", methods={"GET"})
     */
    public function getCommunes(Request $request, CommuneRepository $communeRepository, SerializerInterface $serializer) {
        $communes = $communeRepository->getByCriteria(
            $request->query->has('search') ? $request->query->get('search') : ''
        );
        $json = $serializer->serialize($communes, 'json');
        return new Response($json, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}