<?php

namespace App\Controller\Manager;

use App\Entity\BoardMinute;
use App\Entity\CommunityAdvisor;
use App\Entity\CorsicanAdvisor;
use App\Entity\DepartmentalAdvisor;
use App\Entity\Deputy;
use App\Entity\EuroDeputy;
use App\Entity\Mayor;
use App\Entity\MunicipalAdvisor;
use App\Entity\RegionalAdvisor;
use App\Entity\Senator;
use App\Form\BoardMinuteType;
use App\Repository\AreaRepository;
use App\Repository\BoardMinuteRepository;
use App\Repository\CommuneRepository;
use App\Repository\CommunityAdvisorRepository;
use App\Repository\CorsicanAdvisorRepository;
use App\Repository\DepartmentalAdvisorRepository;
use App\Repository\DepartmentRepository;
use App\Repository\DeputyRepository;
use App\Repository\EuroDeputyRepository;
use App\Repository\MayorRepository;
use App\Repository\MunicipalAdvisorRepository;
use App\Repository\RegionalAdvisorRepository;
use App\Repository\SenatorRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/manager/board/minute")
 */
class BoardMinuteController extends AbstractController
{
    private $templateDir = 'manager/board_minute/';

    /**
     * @Route("/", name="board_minute_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator, SerializerInterface $serializer): Response
    {
        if($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $search = $request->query->get('search')['value'];

            $dql = "SELECT b FROM App\Entity\BoardMinute b LEFT JOIN b.commune c";
            $dql .= " LEFT JOIN b.department d";
            $dql .= " LEFT JOIN b.area a";

            if(!empty($search)) {
                $dql .= " where b.title like '%" . $search . "%'";
                $dql .= " OR b.year like '%" . $search . "%'";
                $dql .= " OR b.month like '%" . $search . "%'";
                $dql .= " OR b.target like '%" . $search . "%'";
                $dql .= " OR b.targetCode like '%" . $search . "%'";
                $dql .= " OR c.name like '%" . $search . "%'";
                $dql .= " OR c.codeInsee like '%" . $search . "%'";
                $dql .= " OR d.name like '%" . $search . "%'";
                $dql .= " OR a.name like '%" . $search . "%'";
            }

            $column = (int)$request->query->get('order')[0]['column'];
            $mode = $request->query->get('order')[0]['dir'];
            if($column === 0) {
                $dql .= " ORDER BY b.id ";
            }
            elseif($column === 1) {
                $dql .= " ORDER BY c.name, c.codeInsee, d.name, a.name ";
            }
            elseif($column === 2) {
                $dql .= " ORDER BY b.title ";
            }
            elseif($column === 3) {
                $dql .= " ORDER BY b.month ";
            }
            else {
                $dql .= " ORDER BY b.year ";
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
            $json = $serializer->serialize($data, 'json');
            return new Response($json, Response::HTTP_OK, ['Content-Type' => 'application/json']);
        }

        return $this->render("{$this->templateDir}index.html.twig", [
        ]);
    }

    /**
     * @Route("/new", name="board_minute_new", methods={"GET","POST"})
     */
    public function new(Request $request,
                        CommuneRepository $communeRepository,
                        DepartmentRepository $departmentRepository,
                        AreaRepository $areaRepository,
                        SluggerInterface $slugger): Response
    {
        $boardMinute = new BoardMinute();
        $form = $this->createForm(BoardMinuteType::class, $boardMinute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $request->request->get('board_minute')['category'];
            $target = (int) $request->request->get('board_minute')['target'];

            if($category === 'commune') {
                $commune = $communeRepository->find($target);
                $boardMinute->setCommune($commune);
            }
            elseif($category === 'department') {
                $department = $departmentRepository->find($target);
                $boardMinute->setDepartment($department);
            }
            else {
                $area = $areaRepository->find($target);
                $boardMinute->setArea($area);
            }

            $files = $form->get('files')->getData();

            $filesize = 0;
            foreach ($files as $boardMinuteFile) {
                $filesize += $boardMinuteFile->getSize();
            }

            $filesize = $filesize / 1000 / 1000;
            if($filesize <= 32) {
                $fileNames = [];
                foreach ($files as $boardMinuteFile) {
                    $filesize += $boardMinuteFile->getSize();
                    $originalFilename = pathinfo($boardMinuteFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $boardMinuteFile->guessExtension();
                    try {
                        $boardMinuteFile->move(
                            $this->getParameter('boards_minutes'),
                            $newFilename
                        );
                    } catch (FileException $e) {

                    }
                    array_push($fileNames, $newFilename);
                }
                $boardMinute->setFileNames($fileNames);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($boardMinute);
                $entityManager->flush();
                $this->addFlash('success', 'Enregistré avec succès');
                return $this->redirectToRoute('board_minute_index');
            }
            else {
                $this->addFlash('danger', 'Taille maximale des fichiers ne doit pas dépasser 32M');
            }
        }

        return $this->render("{$this->templateDir}new.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="board_minute_show", methods={"GET"})
     */
    public function show(BoardMinute $boardMinute): Response
    {
        return $this->render("{$this->templateDir}show.html.twig", [
            'boardMinute' => $boardMinute
        ]);
    }

    /**
     * @Route("/{id}", name="board_minute_delete", methods={"DELETE"})
     */
    public function delete(Request  $request, BoardMinute $boardMinute): Response
    {
        if ($this->isCsrfTokenValid('delete'.$boardMinute->getId(), $request->request->get('_token'))) {
            $path = $this->getParameter('boards_minutes') . '/';

            foreach ($boardMinute->getFileNames() as $fileName) {
                $filePath = $path . $fileName;

                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($boardMinute);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectuée avec succès');
        }
        return $this->redirectToRoute('board_minute_index');
    }


    /**
     * @Route("/{id}/{fileName}", name="board_minute_file_delete", methods={"DELETE"})
     */
    public function deleteFile(BoardMinute $boardMinute, $fileName): Response
    {
        $path = $this->getParameter('boards_minutes') . '/';
        $filePath = $path . $fileName;

        $fileNames = $boardMinute->getFileNames();
        $key = array_search($fileName, $fileNames);
        unset($fileNames[$key]);
        $boardMinute->setFileNames($fileNames);

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        $this->addFlash('success', 'Suppression effectuée avec succès');
        return $this->redirectToRoute('board_minute_show', ['id' => $boardMinute->getId()]);
    }

}
