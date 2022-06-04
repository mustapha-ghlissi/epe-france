<?php

namespace App\Controller\Manager;

use App\Entity\Mayor;
use App\Form\MayorType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/manager/mayor")
 */
class MayorController extends AbstractController
{
    private $templateDir = 'manager/mayor/';

    /**
     * @Route("/", name="mayor_index", methods={"GET", "POST"})
     */
    public function index(Request $request, PaginatorInterface $paginator, SerializerInterface $serializer): Response
    {
        if($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $search = $request->query->get('search')['value'];
            if(!empty($search)) {
                $dql = "SELECT ma FROM App\Entity\Mayor ma where ma.firstName like '%" . $search . "%'";
                $dql .= "OR ma.lastName like '%" . $search . "%'";
                $dql .= "OR ma.gender like '%" . $search . "%'";
                $dql .= "OR ma.birthDate like '%" . $search . "%'";
            }
            else {
                $dql = "SELECT ma FROM App\Entity\Mayor ma";
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
     * @Route("/new", name="mayor_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $mayor = new Mayor();
        $form = $this->createForm(MayorType::class, $mayor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mayor);
            $entityManager->flush();
            $this->addFlash('success', 'Enregistrement effecté avec succès');
            return $this->redirectToRoute('mayor_index');
        }

        return $this->render("{$this->templateDir}new.html.twig", [
            'mayor' => $mayor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="mayor_show", methods={"GET"})
     */
    public function show(Mayor $mayor): Response
    {
        return $this->render("{$this->templateDir}show.html.twig", [
            'mayor' => $mayor,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="mayor_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Mayor $mayor): Response
    {
        $form = $this->createForm(MayorType::class, $mayor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Mise à jour effectée avec succès');
            return $this->redirectToRoute('mayor_index');
        }

        return $this->render("{$this->templateDir}edit.html.twig", [
            'mayor' => $mayor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="mayor_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Mayor $mayor): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mayor->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mayor);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectée avec succès');
        }

        return $this->redirectToRoute('mayor_index');
    }
}
