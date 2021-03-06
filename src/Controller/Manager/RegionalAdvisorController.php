<?php

namespace App\Controller\Manager;

use App\Entity\RegionalAdvisor;
use App\Form\RegionalAdvisorType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/manager/regional/advisor")
 */
class RegionalAdvisorController extends AbstractController
{
    private $templateDir = 'manager/regional_advisor/';

    /**
     * @Route("/", name="regional_advisor_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator, SerializerInterface $serializer): Response
    {
        if($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $search = $request->query->get('search')['value'];

            if($request->query->has('role') && $role = $request->query->get('role')) {
                if($role === "president") {
                    $dql = "SELECT ra FROM App\Entity\RegionalAdvisor ra where ra.functionLabel = 'Président du conseil régional' ";
                    $and = 'AND';
                }
                else {
                    $dql = "SELECT ra FROM App\Entity\RegionalAdvisor ra ";
                }
            }
            else {
                $dql = "SELECT ra FROM App\Entity\RegionalAdvisor ra ";
            }

            if(!empty(trim($search))) {
                $dql .= $and ?? 'WHERE';
                $dql .= " (ra.firstName like '%" . $search . "%'";
                $dql .= "OR ra.lastName like '%" . $search . "%'";
                $dql .= "OR ra.gender like '%" . $search . "%'";
                $dql .= "OR ra.birthDate like '%" . $search . "%')";
            }

            $column = (int)$request->query->get('order')[0]['column'];
            $mode = $request->query->get('order')[0]['dir'];
            if($column === 0) {
                $dql .= " ORDER BY ra.id ";
            }
            elseif($column === 1) {
                $dql .= " ORDER BY ra.firstName ";
            }
            elseif($column === 2) {
                $dql .= " ORDER BY ra.lastName ";
            }
            elseif($column === 3) {
                $dql .= " ORDER BY ra.gender ";
            }
            else {
                $dql .= " ORDER BY ra.birthDate ";
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
     * @Route("/new", name="regional_advisor_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $regionalAdvisor = new RegionalAdvisor();
        $form = $this->createForm(RegionalAdvisorType::class, $regionalAdvisor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($regionalAdvisor);
            $entityManager->flush();
            $this->addFlash('success', 'Enregistrement effecté avec succès');
            return $this->redirectToRoute('regional_advisor_index');
        }

        return $this->render("{$this->templateDir}new.html.twig", [
            'regional_advisor' => $regionalAdvisor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="regional_advisor_show", methods={"GET"})
     */
    public function show(RegionalAdvisor $regionalAdvisor): Response
    {
        return $this->render("{$this->templateDir}show.html.twig", [
            'regional_advisor' => $regionalAdvisor,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="regional_advisor_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RegionalAdvisor $regionalAdvisor): Response
    {
        $form = $this->createForm(RegionalAdvisorType::class, $regionalAdvisor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Mise à jour effectée avec succès');
            return $this->redirectToRoute('regional_advisor_index');
        }

        return $this->render("{$this->templateDir}edit.html.twig", [
            'regional_advisor' => $regionalAdvisor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="regional_advisor_delete", methods={"DELETE"})
     */
    public function delete(Request $request, RegionalAdvisor $regionalAdvisor): Response
    {
        if ($this->isCsrfTokenValid('delete'.$regionalAdvisor->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($regionalAdvisor);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectée avec succès');
        }

        return $this->redirectToRoute('regional_advisor_index');
    }
}
