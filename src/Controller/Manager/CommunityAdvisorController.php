<?php

namespace App\Controller\Manager;

use App\Entity\CommunityAdvisor;
use App\Form\CommunityAdvisorType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/manager/community/advisor")
 */
class CommunityAdvisorController extends AbstractController
{
    private $templateDir = 'manager/community_advisor/';

    /**
     * @Route("/", name="community_advisor_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator, SerializerInterface $serializer): Response
    {

        if($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $search = $request->query->get('search')['value'];
            if(!empty($search)) {
                $dql = "SELECT ca FROM App\Entity\CommunityAdvisor ca where ca.firstName like '%" . $search . "%'";
                $dql .= "OR ca.lastName like '%" . $search . "%'";
                $dql .= "OR ca.gender like '%" . $search . "%'";
                $dql .= "OR ca.birthDate like '%" . $search . "%'";
            }
            else {
                $dql = "SELECT ca FROM App\Entity\CommunityAdvisor ca";
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
     * @Route("/new", name="community_advisor_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $communityAdvisor = new CommunityAdvisor();
        $form = $this->createForm(CommunityAdvisorType::class, $communityAdvisor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($communityAdvisor);
            $entityManager->flush();
            $this->addFlash('success', 'Enregistrement effecté avec succès');
            return $this->redirectToRoute('community_advisor_index');
        }

        return $this->render("{$this->templateDir}new.html.twig", [
            'community_advisor' => $communityAdvisor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="community_advisor_show", methods={"GET"})
     */
    public function show(CommunityAdvisor $communityAdvisor): Response
    {
        return $this->render("{$this->templateDir}show.html.twig", [
            'community_advisor' => $communityAdvisor,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="community_advisor_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CommunityAdvisor $communityAdvisor): Response
    {
        $form = $this->createForm(CommunityAdvisorType::class, $communityAdvisor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Mise à jour effectée avec succès');
            return $this->redirectToRoute('community_advisor_index');
        }

        return $this->render("{$this->templateDir}edit.html.twig", [
            'community_advisor' => $communityAdvisor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="community_advisor_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CommunityAdvisor $communityAdvisor): Response
    {
        if ($this->isCsrfTokenValid('delete'.$communityAdvisor->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($communityAdvisor);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectée avec succès');
        }

        return $this->redirectToRoute('community_advisor_index');
    }
}
