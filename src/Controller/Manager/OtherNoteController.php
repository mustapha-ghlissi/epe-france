<?php

namespace App\Controller\Manager;

use App\Entity\CommunityAdvisor;
use App\Entity\CorsicanAdvisor;
use App\Entity\DepartmentalAdvisor;
use App\Entity\MunicipalAdvisor;
use App\Entity\OtherNote;
use App\Entity\RegionalAdvisor;
use App\Entity\Senator;
use App\Form\SpreadSheetType;
use App\Repository\CommunityAdvisorRepository;
use App\Repository\CorsicanAdvisorRepository;
use App\Repository\DepartmentalAdvisorRepository;
use App\Repository\MunicipalAdvisorRepository;
use App\Repository\OtherNoteRepository;
use App\Repository\RegionalAdvisorRepository;
use App\Repository\SenatorRepository;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Carbon\Carbon;


/**
 * @Route("/manager/note/other")
 */
class OtherNoteController extends AbstractController
{
    private $templateDir = 'manager/other_note/';

    /**
     * @Route("/", name="other_note_index", methods={"GET"})
     */
    public function index(Request $request,
                          PaginatorInterface $paginator,
                          SerializerInterface $serializer,
                          OtherNoteRepository $otherNoteRepository): Response
    {

        if($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $search = $request->query->get('search')['value'];
            $dql = "SELECT n FROM App\Entity\OtherNote n LEFT JOIN n.municipalAdvisor m LEFT JOIN n.communityAdvisor c ";
            $dql .= "LEFT JOIN n.departmentalAdvisor d LEFT JOIN n.regionalAdvisor r LEFT JOIN n.corsicanAdvisor ca LEFT JOIN n.senator s";

            if(!empty($search)) {
                $dql .= "where n.ipAddress like '%" . $search . "%'";
                $dql .= "OR n.evaluationDate like '%" . $search . "%'";
                $dql .= "OR m.firstName like '%" . $search . "%'";
                $dql .= "OR m.lastName like '%" . $search . "%'";

                $dql .= "OR c.firstName like '%" . $search . "%'";
                $dql .= "OR c.lastName like '%" . $search . "%'";

                $dql .= "OR d.firstName like '%" . $search . "%'";
                $dql .= "OR d.lastName like '%" . $search . "%'";

                $dql .= "OR r.firstName like '%" . $search . "%'";
                $dql .= "OR r.lastName like '%" . $search . "%'";

                $dql .= "OR ca.firstName like '%" . $search . "%'";
                $dql .= "OR ca.lastName like '%" . $search . "%'";

                $dql .= "OR s.firstName like '%" . $search . "%'";
                $dql .= "OR s.lastName like '%" . $search . "%'";
            }

            $column = (int)$request->query->get('order')[0]['column'];
            $mode = $request->query->get('order')[0]['dir'];
            if($column === 0) {
                $dql .= " ORDER BY n.id ";
            }
            elseif($column === 1) {
                $dql .= " ORDER BY n.ipAddress ";
            }
            elseif($column === 2) {
                $dql .= " ORDER BY n.evaluationDate ";
            }
            elseif($column === 3) {
                $dql .= " ORDER BY m.firstName, c.firstName, d.firstName, r.firstName, ca.firstName, s.firstName ";
            }
            else {
                $dql .= " ORDER BY m.lastName, c.lastName, d.lastName, r.lastName, ca.lastName, s.lastName ";
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
            $json = $serializer->serialize($data, 'json', array('groups' => 'note:read'));
            return new Response($json, Response::HTTP_OK, ['Content-Type' => 'application/json']);
        }

        return $this->render("{$this->templateDir}index.html.twig", [
            'other_notes' => $otherNoteRepository->getCount(),
        ]);
    }

    private function getFieldValue($value, $filter = null, $isDate = false) {
        if(trim($value) !== '') {
            if($filter) {
                return filter_var($value, $filter);
            }
            elseif($isDate) {
                return new Carbon($value);
            }
            return $value;
        }
        return null;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @Route("/import", name="other_note_import", methods={"GET", "POST"})
     */
    public function import(Request $request,
                           OtherNoteRepository $otherNoteRepository,
                           SenatorRepository $senatorRepository,
                           DepartmentalAdvisorRepository $departmentalAdvisorRepository,
                           RegionalAdvisorRepository $regionalAdvisorRepository, MunicipalAdvisorRepository $municipalAdvisorRepository,
                           CorsicanAdvisorRepository $corsicanAdvisorRepository, CommunityAdvisorRepository $communityAdvisorRepository
        ) {

        $spreadForm = $this->createForm(SpreadSheetType::class, null, [
            'method' => Request::METHOD_POST
        ]);
        $spreadForm->handleRequest($request);
        if($spreadForm->isSubmitted() && $spreadForm->isValid()) {

            $em = $this->getDoctrine()->getManager();
            /**
             * @var UploadedFile $spreadsheetFile
             */
            $spreadsheetFile = $spreadForm->get('file')->getData();
            $sheetName = '10- 2 Notes des autres élus loc';
            $inputFileType = IOFactory::identify($spreadsheetFile);
            $reader = IOFactory::createReader($inputFileType);
            $reader->setLoadSheetsOnly($sheetName);
            $spreadsheet = $reader->load($spreadsheetFile);
            $sheet = $spreadsheet->getActiveSheet();
            $sheetRows = $sheet->getHighestRow();
            $nbImportedNote = 0;
            for($i = 2; $i <= $sheetRows; $i++) {
                $lastName = $sheet->getCell('A' . $i)->getValue();
                $firstName = $sheet->getCell('B' . $i)->getValue();

                $regAdvisor = $regionalAdvisorRepository->getByName(['lastName' => '%' . $lastName . '%', 'firstName' => '%' . $firstName . '%']);
                $otherNote = new OtherNote();
                $otherNote->setIpAddress($sheet->getCell('C' . $i)->getValue())
                        ->setEvaluationDate($this->getFieldValue($sheet->getCell('D' . $i)->getFormattedValue(), null, true))
                        ->setPresenceNumber(filter_var($sheet->getCell('E' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                    ->setAmendmentsNumber(filter_var($sheet->getCell('F' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                    ->setAchievementsNumber(filter_var($sheet->getCell('G' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                    ->setWorksNumber(filter_var($sheet->getCell('H' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT));

                if($regAdvisor instanceof RegionalAdvisor) {
                    $nbImportedNote ++;
                    $otherNote->setRegionalAdvisor($regAdvisor);
                    $em->persist($otherNote);
                }
                else {
                    $depAdvisor = $departmentalAdvisorRepository->getByName(['lastName' => '%' . $lastName . '%', 'firstName' => '%' . $firstName . '%']);
                    if($depAdvisor instanceof DepartmentalAdvisor) {
                        $nbImportedNote ++;
                        $otherNote->setDepartmentalAdvisor($depAdvisor);
                        $em->persist($otherNote);
                    }
                    else {
                        $comAdvisor = $communityAdvisorRepository->getByName(['lastName' => '%' . $lastName . '%', 'firstName' => '%' . $firstName . '%']);
                        if($comAdvisor instanceof CommunityAdvisor) {
                            $nbImportedNote ++;
                            $otherNote->setCommunityAdvisor($comAdvisor);
                            $em->persist($otherNote);
                        }
                        else {
                            $corsicanAdvisor = $corsicanAdvisorRepository->getByName(['lastName' => '%' . $lastName . '%', 'firstName' => '%' . $firstName . '%']);
                            if($corsicanAdvisor instanceof CorsicanAdvisor) {
                                $nbImportedNote ++;
                                $otherNote->setCorsicanAdvisor($corsicanAdvisor);
                                $em->persist($otherNote);
                            }
                            else {
                                $munAdvisor = $municipalAdvisorRepository->getByName(['lastName' => '%' . $lastName . '%', 'firstName' => '%' . $firstName . '%']);
                                if($munAdvisor instanceof MunicipalAdvisor) {
                                    $nbImportedNote ++;
                                    $otherNote->setMunicipalAdvisor($munAdvisor);
                                    $em->persist($otherNote);
                                }
                                else{
                                    $senAdvisor = $senatorRepository->getByName(['lastName' => '%' . $lastName . '%', 'firstName' => '%' . $firstName . '%']);
                                    if($senAdvisor instanceof Senator) {
                                        $nbImportedNote ++;
                                        $otherNote->setSenator($senAdvisor);
                                        $em->persist($otherNote);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if($nbImportedNote > 0) {
                $em->flush();
                $this->addFlash('success', 'Importation effectuée avec succès.');
            }
            else {
                $this->addFlash('info', 'Les notes ne correspondent à aucun député');
            }
            return $this->redirectToRoute('other_note_index');
        }
        return $this->render("{$this->templateDir}import.html.twig",
            [
                'form' => $spreadForm->createView()
            ]);
    }

    /**
     * @param Request $request
     * @param OtherNoteRepository $otherNoteRepository
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @Route("/export", name="other_note_export", methods={"GET", "POST"})
     */
    public function export(Request $request, OtherNoteRepository $otherNoteRepository) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('10- 2 Notes des autres élus loc');
        $notes = $otherNoteRepository->findAll();
        $writer = new Xlsx($spreadsheet);

        $sheet->setCellValue('A1', 'Nom')
            ->setCellValue('B1', 'Prénom')
            ->setCellValue('C1', 'Adresse Ip')
            ->setCellValue('D1', 'Date d\'évaluation')
            ->setCellValue('E1', 'Nombre de présence physique aux conseils et aux séances depuis le début de son mandat')
            ->setCellValue('F1', 'Nombre d\'Amendements depuis le début de son mandat')
            ->setCellValue('G1', 'Nombre de travaux dirigés depuis le début de son mandat')
            ->setCellValue('H1', 'Nombre de projets réalisés depuis le début de son mandat')
            ->setCellValue('I1', 'Libellé de la commune')
            ->setCellValue('J1', 'Libellé de département')
            ->setCellValue('K1', 'Libellé de la région');

        foreach ($notes as $key => $note) {
            $row = $key + 2;

            if($note->getSenator()) {
                $sheet->setCellValue("A{$row}", $note->getSenator()->getLastName())
                    ->setCellValue("B$row", $note->getSenator()->getFirstName())
                    ->setCellValue("I$row", '')
                    ->setCellValue("J$row", $note->getSenator()->getDepartmentLabel())
                    ->setCellValue("K$row", $note->getSenator()->getAreaLabel());
            }
            elseif ($note->getCorsicanAdvisor()) {
                $sheet->setCellValue("A{$row}", $note->getCorsicanAdvisor()->getLastName())
                    ->setCellValue("B$row", $note->getCorsicanAdvisor()->getFirstName())
                    ->setCellValue("I$row", '')
                    ->setCellValue("J$row", '')
                    ->setCellValue("K$row", '');
            }
            elseif ($note->getCommunityAdvisor()) {
                $sheet->setCellValue("A{$row}", $note->getCommunityAdvisor()->getLastName())
                    ->setCellValue("B$row", $note->getCommunityAdvisor()->getFirstName())
                    ->setCellValue("I$row", $note->getCommunityAdvisor()->getCommuneLabel())
                    ->setCellValue("J$row", $note->getCommunityAdvisor()->getDepartmentLabel())
                    ->setCellValue("K$row", $note->getCommunityAdvisor()->getAreaLabel());
            }
            elseif ($note->getRegionalAdvisor()) {
                $sheet->setCellValue("A{$row}", $note->getRegionalAdvisor()->getLastName())
                    ->setCellValue("B$row", $note->getRegionalAdvisor()->getFirstName())
                    ->setCellValue("I$row", '')
                    ->setCellValue("J$row", $note->getRegionalAdvisor()->getDepartmentLabel())
                    ->setCellValue("K$row", $note->getRegionalAdvisor()->getAreaLabel())
                ;
            }
            elseif ($note->getMunicipalAdvisor()) {
                $sheet->setCellValue("A{$row}", $note->getMunicipalAdvisor()->getLastName())
                    ->setCellValue("B$row", $note->getMunicipalAdvisor()->getFirstName())
                    ->setCellValue("I$row", $note->getMunicipalAdvisor()->getCommuneLabel())
                    ->setCellValue("J$row", $note->getMunicipalAdvisor()->getDepartmentLabel())
                    ->setCellValue("K$row", $note->getMunicipalAdvisor()->getAreaLabel())
                ;
            }
            else {
                $sheet->setCellValue("A{$row}", $note->getDepartmentalAdvisor()->getLastName())
                    ->setCellValue("B$row", $note->getDepartmentalAdvisor()->getFirstName())
                    ->setCellValue("I$row", '')
                    ->setCellValue("J$row", $note->getDepartmentalAdvisor()->getDepartmentLabel())
                    ->setCellValue("K$row", $note->getDepartmentalAdvisor()->getAreaLabel())
                ;
            }

            $sheet->setCellValue("C$row", $note->getIpAddress())
                ->setCellValue("D$row", $note->getEvaluationDate())
                ->setCellValue("E$row", $note->getPresenceNumber())
                ->setCellValue("F$row", $note->getAmendmentsNumber())
                ->setCellValue("G$row", $note->getAchievementsNumber())
                ->setCellValue("H$row", $note->getWorksNumber());
        }
        $fileName = 'autres-notes.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);

        $spreadsheet->disconnectWorksheets();

        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/{id}", name="other_note_show", methods={"GET"})
     */
    public function show(OtherNote $otherNote): Response
    {
        return $this->render("{$this->templateDir}show.html.twig", [
            'other_note' => $otherNote,
        ]);
    }

    /**
     * @Route("/{id}", name="other_note_delete", methods={"DELETE"})
     */
    public function delete(Request $request, OtherNote $otherNote): Response
    {
        if ($this->isCsrfTokenValid('delete'.$otherNote->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($otherNote);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectuée avec succès.');
        }

        return $this->redirectToRoute('other_note_index');
    }
}
