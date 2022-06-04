<?php

namespace App\Controller\Manager;

use App\Entity\Senator;
use App\Form\SenatorType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/manager/senator")
 */
class SenatorController extends AbstractController
{
    private $templateDir = 'manager/senator/';

    /**
     * @Route("/", name="senator_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator, SerializerInterface $serializer): Response
    {
        if($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $search = $request->query->get('search')['value'];
            if(!empty($search)) {
                $dql = "SELECT s FROM App\Entity\Senator s where s.firstName like '%" . $search . "%'";
                $dql .= "OR s.lastName like '%" . $search . "%'";
                $dql .= "OR s.gender like '%" . $search . "%'";
                $dql .= "OR s.birthDate like '%" . $search . "%'";
            }
            else {
                $dql = "SELECT s FROM App\Entity\Senator s";
            }

            $column = (int)$request->query->get('order')[0]['column'];
            $mode = $request->query->get('order')[0]['dir'];
            if($column === 0) {
                $dql .= " ORDER BY s.id ";
            }
            elseif($column === 1) {
                $dql .= " ORDER BY s.firstName ";
            }
            elseif($column === 2) {
                $dql .= " ORDER BY s.lastName ";
            }
            elseif($column === 3) {
                $dql .= " ORDER BY s.gender ";
            }
            else {
                $dql .= " ORDER BY s.birthDate ";
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
     * @Route("/new", name="senator_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $senator = new Senator();
        $form = $this->createForm(SenatorType::class, $senator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($senator);
            $entityManager->flush();
            $this->addFlash('success', 'Enregistrement effecté avec succès');
            return $this->redirectToRoute('senator_index');
        }

        return $this->render("{$this->templateDir}new.html.twig", [
            'senator' => $senator,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="senator_show", methods={"GET"})
     */
    public function show(Senator $senator): Response
    {
        return $this->render("{$this->templateDir}show.html.twig", [
            'senator' => $senator,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="senator_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Senator $senator): Response
    {
        $form = $this->createForm(SenatorType::class, $senator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Mise à jour effectée avec succès');
            return $this->redirectToRoute('senator_index');
        }

        return $this->render("{$this->templateDir}edit.html.twig", [
            'senator' => $senator,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="senator_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Senator $senator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$senator->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($senator);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectée avec succès');
        }

        return $this->redirectToRoute('senator_index');
    }
}
