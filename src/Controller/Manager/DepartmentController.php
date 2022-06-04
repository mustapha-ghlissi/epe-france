<?php


namespace App\Controller\Manager;


use App\Repository\DepartmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class DepartmentController
 * @package App\Controller\Manager
 * @Route("/manager/department")
 */
class DepartmentController extends AbstractController
{
    /**
     * @param DepartmentRepository $departmentRepository
     * @param SerializerInterface $serializer
     * @return Response
     * @Route(name="manager_list_departments", methods={"GET"})
     */
    public function getDepartments(Request $request, DepartmentRepository $departmentRepository, SerializerInterface $serializer) {
        $departments = $departmentRepository->getByCriteria(
            $request->query->has('search') ? $request->query->get('search') : ''
        );
        $json = $serializer->serialize($departments, 'json');
        return new Response($json, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}