<?php

namespace App\Controller\Manager;

use App\Entity\EuroDeputy;
use App\Entity\EuroDeputyNote;
use App\Form\SpreadSheetType;
use App\Repository\EuroDeputyNoteRepository;
use App\Repository\EuroDeputyRepository;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Carbon\Carbon;

/**
 * @Route("/manager/note/euro/deputy")
 */
class EuroDeputyNoteController extends AbstractController
{
    private $templateDir = 'manager/euro_deputy_note/';

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param SerializerInterface $serializer
     * @param EuroDeputyNoteRepository $euroDeputyNoteRepository
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @Route("/", name="euro_deputy_note_index", methods={"GET"})
     */
    public function index(Request $request,
                          PaginatorInterface $paginator,
                          SerializerInterface $serializer,
                          EuroDeputyNoteRepository $euroDeputyNoteRepository): Response
    {

        if($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $search = $request->query->get('search')['value'];

            $dql = "SELECT n FROM App\Entity\EuroDeputyNote n INNER JOIN n.euroDeputy d ";
            if(!empty($search)) {
                $dql .= "where n.ipAddress like '%" . $search . "%'";
                $dql .= "OR n.evaluationDate like '%" . $search . "%'";
                $dql .= "OR d.firstName like '%" . $search . "%'";
                $dql .= "OR d.lastName like '%" . $search . "%'";
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
                $dql .= " ORDER BY d.firstName ";
            }
            else {
                $dql .= " ORDER BY d.lastName ";
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
            'euro_deputy_notes' => $euroDeputyNoteRepository->getCount(),
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
     * @Route("/import", name="euro_deputy_note_import", methods={"GET", "POST"})
     */
    public function import(Request $request, EuroDeputyRepository $euroDeputyRepository, EuroDeputyNoteRepository $euroDeputyNoteRepository) {
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
            $sheetName = '10- 4 Notes des députés Europ';
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
                $euroDeputy = $euroDeputyRepository->getByName(['lastName' => '%' . $lastName . '%', 'firstName' => '%' . $firstName . '%']);

                if($euroDeputy instanceof EuroDeputy) {
                    $nbImportedNote ++;
                    $euroDeputyNote = new EuroDeputyNote();
                    $euroDeputyNote
                        ->setEuroDeputy($euroDeputy)
                        ->setIpAddress($sheet->getCell('C' . $i)->getValue())
                        ->setEvaluationDate($this->getFieldValue($sheet->getCell('D' . $i)->getFormattedValue(), null, true))
                        ->setPhysicalPresence(filter_var($sheet->getCell('E' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setAmendmentsNumber(filter_var($sheet->getCell('F' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setVotesNumber(filter_var($sheet->getCell('G' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setParticipationsNumber(filter_var($sheet->getCell('H' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setSuggestionsNumber(filter_var($sheet->getCell('I' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setQuestionsNumber(filter_var($sheet->getCell('J' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT));
                    $em->persist($euroDeputyNote);
                }
            }
            if($nbImportedNote > 0) {
                $em->flush();
                $this->addFlash('success', 'Importation effectuée avec succès.');
            }
            else {
                $this->addFlash('info', 'Les notes ne correspondent à aucun député');
            }

            return $this->redirectToRoute('euro_deputy_note_index');
        }
        return $this->render("{$this->templateDir}import.html.twig",
            [
                'form' => $spreadForm->createView()
            ]);
    }

    /**
     * @param Request $request
     * @param EuroDeputyNoteRepository $euroDeputyNoteRepository
     * @return BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @Route("/export", name="euro_deputy_note_export", methods={"GET", "POST"})
     */
    public function export(Request $request, EuroDeputyNoteRepository $euroDeputyNoteRepository) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('10- 4 Notes des députés Europ');
        $notes = $euroDeputyNoteRepository->findAll();
        $writer = new Xlsx($spreadsheet);

        $sheet->setCellValue('A1', 'Nom')
            ->setCellValue('B1', 'Prénom')
            ->setCellValue('C1', 'Adresse Ip')
            ->setCellValue('D1', 'Date d\'évaluation')
            ->setCellValue('E1', 'Présence physique aux plénières depuis le début de son mandat')
            ->setCellValue('F1', 'Nombre d\'amendements depuis le début de son mandat')
            ->setCellValue('G1', 'Nombre de votes depuis le début de son mandat')
            ->setCellValue('H1', 'Nombre de participation aux travaux dirigés dans les commissions depuis le début de son mandat')
            ->setCellValue('I1', 'Nombre de propositions de loi déposé depuis le début de son mandat')
            ->setCellValue('J1', 'Nombre de questions écrites et orales depuis le début de son mandat')
        ;

        foreach ($notes as $key => $note) {
            $row = $key + 2;
            $sheet->setCellValue("A{$row}", $note->getEuroDeputy()->getLastName())
                ->setCellValue("B$row", $note->getEuroDeputy()->getFirstName())
                ->setCellValue("C$row", $note->getIpAddress())
                ->setCellValue("D$row", $note->getEvaluationDate())
                ->setCellValue("E$row", $note->getPhysicalPresence())
                ->setCellValue("F$row", $note->getAmendmentsNumber())
                ->setCellValue("G$row", $note->getVotesNumber())
                ->setCellValue("H$row", $note->getParticipationsNumber())
                ->setCellValue("I$row", $note->getSuggestionsNumber())
                ->setCellValue("J$row", $note->getQuestionsNumber())
            ;
        }
        $fileName = 'notes-euro-deputes.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);

        $spreadsheet->disconnectWorksheets();

        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/{id}", name="euro_deputy_note_show", methods={"GET"})
     */
    public function show(EuroDeputyNote $euroDeputyNote): Response
    {
        return $this->render("{$this->templateDir}show.html.twig", [
            'euro_deputy_note' => $euroDeputyNote,
        ]);
    }

    /**
     * @Route("/{id}", name="euro_deputy_note_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EuroDeputyNote $euroDeputyNote): Response
    {
        if ($this->isCsrfTokenValid('delete'.$euroDeputyNote->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($euroDeputyNote);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectuée avec succès.');
        }

        return $this->redirectToRoute('euro_deputy_note_index');
    }
}
