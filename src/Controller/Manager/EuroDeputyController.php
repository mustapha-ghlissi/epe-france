<?php

namespace App\Controller\Manager;

use App\Entity\EuroDeputy;
use App\Form\EuroDeputyType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/manager/euro/deputy")
 */
class EuroDeputyController extends AbstractController
{
    private $templateDir = 'manager/euro_deputy/';


    /**
     * @Route("/", name="euro_deputy_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator, SerializerInterface $serializer): Response
    {
        if($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $search = $request->query->get('search')['value'];
            if(!empty($search)) {
                $dql = "SELECT d FROM App\Entity\EuroDeputy d where d.firstName like '%" . $search . "%'";
                $dql .= "OR d.lastName like '%" . $search . "%'";
                $dql .= "OR d.gender like '%" . $search . "%'";
                $dql .= "OR d.birthDate like '%" . $search . "%'";
            }
            else {
                $dql = "SELECT d FROM App\Entity\EuroDeputy d";
            }

            $column = (int)$request->query->get('order')[0]['column'];
            $mode = $request->query->get('order')[0]['dir'];
            if($column === 0) {
                $dql .= " ORDER BY d.id ";
            }
            elseif($column === 1) {
                $dql .= " ORDER BY d.firstName ";
            }
            elseif($column === 2) {
                $dql .= " ORDER BY d.lastName ";
            }
            elseif($column === 3) {
                $dql .= " ORDER BY d.gender ";
            }
            else {
                $dql .= " ORDER BY d.birthDate ";
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
     * @Route("/new", name="euro_deputy_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $euroDeputy = new EuroDeputy();
        $form = $this->createForm(EuroDeputyType::class, $euroDeputy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($euroDeputy);
            $entityManager->flush();
            $this->addFlash('success', 'Enregistrement effecté avec succès');
            return $this->redirectToRoute('euro_deputy_index');
        }

        return $this->render("{$this->templateDir}new.html.twig", [
            'euro_deputy' => $euroDeputy,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="euro_deputy_show", methods={"GET"})
     */
    public function show(EuroDeputy $euroDeputy): Response
    {
        return $this->render("{$this->templateDir}show.html.twig", [
            'euro_deputy' => $euroDeputy,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="euro_deputy_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EuroDeputy $euroDeputy): Response
    {
        $form = $this->createForm(EuroDeputyType::class, $euroDeputy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Mise à jour effectée avec succès');
            return $this->redirectToRoute('euro_deputy_index');
        }

        return $this->render("{$this->templateDir}edit.html.twig", [
            'euro_deputy' => $euroDeputy,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="euro_deputy_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EuroDeputy $euroDeputy): Response
    {
        if ($this->isCsrfTokenValid('delete'.$euroDeputy->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($euroDeputy);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectée avec succès');
        }

        return $this->redirectToRoute('euro_deputy_index');
    }
}
