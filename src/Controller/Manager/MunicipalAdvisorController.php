<?php

namespace App\Controller\Manager;

use App\Entity\MunicipalAdvisor;
use App\Form\MunicipalAdvisorType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/manager/municipal/advisor")
 */
class MunicipalAdvisorController extends AbstractController
{
    private $templateDir = 'manager/municipal_advisor/';

    /**
     * @Route("/", name="municipal_advisor_index", methods={"GET"})
     */
    public function index(Request $request,
                          PaginatorInterface $paginator, SerializerInterface $serializer): Response
    {
        if($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $search = $request->query->get('search')['value'];
            if(!empty($search)) {
                $dql = "SELECT ma FROM App\Entity\MunicipalAdvisor ma where ma.firstName like '%" . $search . "%'";
                $dql .= "OR ma.lastName like '%" . $search . "%'";
                $dql .= "OR ma.gender like '%" . $search . "%'";
                $dql .= "OR ma.birthDate like '%" . $search . "%'";
            }
            else {
                $dql = "SELECT ma FROM App\Entity\MunicipalAdvisor ma";
            }

            $column = (int)$request->query->get('order')[0]['column'];
            $mode = $request->query->get('order')[0]['dir'];
            if($column === 0) {
                $dql .= " ORDER BY ma.id ";
            }
            elseif($column === 1) {
                $dql .= " ORDER BY ma.firstName ";
            }
            elseif($column === 2) {
                $dql .= " ORDER BY ma.lastName ";
            }
            elseif($column === 3) {
                $dql .= " ORDER BY ma.gender ";
            }
            else {
                $dql .= " ORDER BY ma.birthDate ";
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
     * @Route("/new", name="municipal_advisor_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $municipalAdvisor = new MunicipalAdvisor();
        $form = $this->createForm(MunicipalAdvisorType::class, $municipalAdvisor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($municipalAdvisor);
            $entityManager->flush();
            $this->addFlash('success', 'Enregistrement effecté avec succès');
            return $this->redirectToRoute('municipal_advisor_index');
        }

        return $this->render("{$this->templateDir}new.html.twig", [
            'municipal_advisor' => $municipalAdvisor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="municipal_advisor_show", methods={"GET"})
     */
    public function show(MunicipalAdvisor $municipalAdvisor): Response
    {
        return $this->render("{$this->templateDir}show.html.twig", [
            'municipal_advisor' => $municipalAdvisor,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="municipal_advisor_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MunicipalAdvisor $municipalAdvisor): Response
    {
        $form = $this->createForm(MunicipalAdvisorType::class, $municipalAdvisor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Mise à jour effectée avec succès');
            return $this->redirectToRoute('municipal_advisor_index');
        }

        return $this->render("{$this->templateDir}edit.html.twig", [
            'municipal_advisor' => $municipalAdvisor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="municipal_advisor_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MunicipalAdvisor $municipalAdvisor): Response
    {
        if ($this->isCsrfTokenValid('delete'.$municipalAdvisor->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($municipalAdvisor);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectée avec succès');
        }

        return $this->redirectToRoute('municipal_advisor_index');
    }
}
