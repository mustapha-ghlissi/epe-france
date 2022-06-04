<?php

namespace App\Controller\Manager;

use App\Entity\DepartmentalAdvisor;
use App\Form\DepartmentalAdvisorType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/manager/departmental/advisor")
 */
class DepartmentalAdvisorController extends AbstractController
{
    private $templateDir = 'manager/departmental_advisor/';

    /**
     * @Route("/", name="departmental_advisor_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator, SerializerInterface $serializer): Response
    {
        if($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $search = $request->query->get('search')['value'];

            if($request->query->has('role') && $role = $request->query->get('role')) {
                if($role === "president") {
                    $dql = "SELECT da FROM App\Entity\DepartmentalAdvisor da where da.functionLabel = 'Président du conseil départemental' ";
                    $and = 'AND';
                }
                else {
                    $dql = "SELECT da FROM App\Entity\DepartmentalAdvisor da ";
                }
            }
            else {
                $dql = "SELECT da FROM App\Entity\DepartmentalAdvisor da ";
            }

            if(!empty(trim($search))) {
                $dql .= $and ?? 'WHERE';
                $dql .= " (da.firstName like '%" . $search . "%'";
                $dql .= "OR da.lastName like '%" . $search . "%'";
                $dql .= "OR da.gender like '%" . $search . "%'";
                $dql .= "OR da.birthDate like '%" . $search . "%')";
            }

            $column = (int)$request->query->get('order')[0]['column'];
            $mode = $request->query->get('order')[0]['dir'];
            if($column === 0) {
                $dql .= " ORDER BY da.id ";
            }
            elseif($column === 1) {
                $dql .= " ORDER BY da.firstName ";
            }
            elseif($column === 2) {
                $dql .= " ORDER BY da.lastName ";
            }
            elseif($column === 3) {
                $dql .= " ORDER BY da.gender ";
            }
            else {
                $dql .= " ORDER BY da.birthDate ";
            }

            $dql .= strtoupper($mode);

            $query = $em->createQuery($dql);
            $start = (int)$request->query->getInt('start', 0);
            $limit = (int)$request->query->getInt('length', 10);
            $page = ($start / $limit) + 1;
            $data = $paginator->paginate($query, $page, $limit);

            $recordsTotal = $data->getTotalItemCount();
            $recordsFiltered = $data->getTotalItemCount();
            $data = $data->getItems();



            $data = [
                'draw' => (int)$request->query->getInt('draw'),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data
            ];
            $json = $serializer->serialize($data, 'json', array('groups' => 'index:read'));
            return new Response($json, Response::HTTP_OK, ['Content-Type' => 'application/json']);
        }

        return $this->render("{$this->templateDir}index.html.twig");
    }

    /**
     * @Route("/new", name="departmental_advisor_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $departmentalAdvisor = new DepartmentalAdvisor();
        $form = $this->createForm(DepartmentalAdvisorType::class, $departmentalAdvisor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($departmentalAdvisor);
            $entityManager->flush();
            $this->addFlash('success', 'Enregistrement effecté avec succès');
            return $this->redirectToRoute('departmental_advisor_index');
        }

        return $this->render("{$this->templateDir}new.html.twig", [
            'departmental_advisor' => $departmentalAdvisor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="departmental_advisor_show", methods={"GET"})
     */
    public function show(DepartmentalAdvisor $departmentalAdvisor): Response
    {
        return $this->render("{$this->templateDir}show.html.twig", [
            'departmental_advisor' => $departmentalAdvisor,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="departmental_advisor_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, DepartmentalAdvisor $departmentalAdvisor): Response
    {
        $form = $this->createForm(DepartmentalAdvisorType::class, $departmentalAdvisor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Mise à jour effectée avec succès');
            return $this->redirectToRoute('departmental_advisor_index');
        }

        return $this->render("{$this->templateDir}edit.html.twig", [
            'departmental_advisor' => $departmentalAdvisor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="departmental_advisor_delete", methods={"DELETE"})
     */
    public function delete(Request $request, DepartmentalAdvisor $departmentalAdvisor): Response
    {
        if ($this->isCsrfTokenValid('delete'.$departmentalAdvisor->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($departmentalAdvisor);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectée avec succès');
        }

        return $this->redirectToRoute('departmental_advisor_index');
    }
}
