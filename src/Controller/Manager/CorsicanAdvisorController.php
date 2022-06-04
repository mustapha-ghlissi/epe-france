<?php

namespace App\Controller\Manager;

use App\Entity\CorsicanAdvisor;
use App\Form\CorsicanAdvisorType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/manager/corsican/advisor")
 */
class CorsicanAdvisorController extends AbstractController
{
    private $templateDir = 'manager/corsican_advisor/';

    /**
     * @Route("/", name="corsican_advisor_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator, SerializerInterface $serializer): Response
    {
        if($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $search = $request->query->get('search')['value'];
            if(!empty($search)) {
                $dql = "SELECT ca FROM App\Entity\CorsicanAdvisor ca where ca.firstName like '%" . $search . "%'";
                $dql .= "OR ca.lastName like '%" . $search . "%'";
                $dql .= "OR ca.gender like '%" . $search . "%'";
                $dql .= "OR ca.birthDate like '%" . $search . "%'";
            }
            else {
                $dql = "SELECT ca FROM App\Entity\CorsicanAdvisor ca";
            }

            $column = (int)$request->query->get('order')[0]['column'];
            $mode = $request->query->get('order')[0]['dir'];
            if($column === 0) {
                $dql .= " ORDER BY ca.id ";
            }
            elseif($column === 1) {
                $dql .= " ORDER BY ca.firstName ";
            }
            elseif($column === 2) {
                $dql .= " ORDER BY ca.lastName ";
            }
            elseif($column === 3) {
                $dql .= " ORDER BY ca.gender ";
            }
            else {
                $dql .= " ORDER BY ca.birthDate ";
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
     * @Route("/new", name="corsican_advisor_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $corsicanAdvisor = new CorsicanAdvisor();
        $form = $this->createForm(CorsicanAdvisorType::class, $corsicanAdvisor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($corsicanAdvisor);
            $entityManager->flush();
            $this->addFlash('success', 'Enregistrement effecté avec succès');
            return $this->redirectToRoute('corsican_advisor_index');
        }

        return $this->render("{$this->templateDir}new.html.twig", [
            'corsican_advisor' => $corsicanAdvisor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="corsican_advisor_show", methods={"GET"})
     */
    public function show(CorsicanAdvisor $corsicanAdvisor): Response
    {
        return $this->render("{$this->templateDir}show.html.twig", [
            'corsican_advisor' => $corsicanAdvisor,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="corsican_advisor_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CorsicanAdvisor $corsicanAdvisor): Response
    {
        $form = $this->createForm(CorsicanAdvisorType::class, $corsicanAdvisor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Mise à jour effectée avec succès');
            return $this->redirectToRoute('corsican_advisor_index');
        }

        return $this->render("{$this->templateDir}edit.html.twig", [
            'corsican_advisor' => $corsicanAdvisor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="corsican_advisor_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CorsicanAdvisor $corsicanAdvisor): Response
    {
        if ($this->isCsrfTokenValid('delete'.$corsicanAdvisor->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($corsicanAdvisor);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectée avec succès');
        }

        return $this->redirectToRoute('corsican_advisor_index');
    }
}
