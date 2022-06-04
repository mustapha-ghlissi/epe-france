<?php


namespace App\Controller\Manager;


use App\Repository\AreaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class DepartmentController
 * @package App\Controller\Manager
 * @Route("/manager/area")
 */
class AreaController extends AbstractController
{
    /**
     * @param AreaRepository $areaRepository
     * @param SerializerInterface $serializer
     * @return Response
     * @Route(name="manager_list_areas", methods={"GET"})
     */
    public function getAreas(Request $request, AreaRepository $areaRepository, SerializerInterface $serializer) {

        $areas = $areaRepository->getByCriteria(
            $request->query->has('search') ? $request->query->get('search') : ''
        );
        $json = $serializer->serialize($areas, 'json');
        return new Response($json, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}