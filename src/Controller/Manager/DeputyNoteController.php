<?php

namespace App\Controller\Manager;

use App\Entity\Deputy;
use App\Entity\DeputyNote;
use App\Form\SpreadSheetType;
use App\Repository\DeputyNoteRepository;
use App\Repository\DeputyRepository;
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
 * @Route("/manager/note/deputy")
 */
class DeputyNoteController extends AbstractController
{
    private $templateDir = 'manager/deputy_note/';

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param DeputyNoteRepository $deputyNoteRepository
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @Route("/", name="deputy_note_index", methods={"GET"})
     */
    public function index(
        Request $request,
        PaginatorInterface $paginator,
        SerializerInterface $serializer,
        DeputyNoteRepository $deputyNoteRepository): Response
    {
        if($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $search = $request->query->get('search')['value'];

            $dql = "SELECT n FROM App\Entity\DeputyNote n INNER JOIN n.deputy d ";
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

        return $this->render("{$this->templateDir}index.html.twig",[
            'deputy_notes' => $deputyNoteRepository->getCount()
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
     * @Route("/import", name="deputy_note_import", methods={"GET", "POST"})
     */
    public function import(Request $request, DeputyRepository $deputyRepository, DeputyNoteRepository $deputyNoteRepository) {
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
            $sheetName = '10- 3 Notes des députés nat';
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
                $deputy = $deputyRepository->getByName(['lastName' => '%' . $lastName . '%', 'firstName' => '%' . $firstName . '%']);
                if($deputy instanceof Deputy) {
                    $nbImportedNote ++;
                    $deputyNote = new DeputyNote();
                    $deputyNote
                        ->setDeputy($deputy)
                        ->setIpAddress($sheet->getCell('C' . $i)->getValue())
                        ->setEvaluationDate($this->getFieldValue($sheet->getCell('D' . $i)->getFormattedValue(), null, true))
                        ->setPresenceNumber(filter_var($sheet->getCell('E' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setAmendmentsNumber(filter_var($sheet->getCell('F' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setVotesNumber(filter_var($sheet->getCell('G' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setParticipationsNumber(filter_var($sheet->getCell('H' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setSuggestionsNumber(filter_var($sheet->getCell('I' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setReportsNumber(filter_var($sheet->getCell('J' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setQuestionsNumber(filter_var($sheet->getCell('K' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT));

                    $em->persist($deputyNote);
                }
            }

            if($nbImportedNote > 0) {
                $em->flush();
                $this->addFlash('success', 'Importation effectuée avec succès.');
            }
            else {
                $this->addFlash('info', 'Les notes ne correspondent à aucun député');
            }
            return $this->redirectToRoute('deputy_note_index');
        }
        return $this->render("{$this->templateDir}import.html.twig",
            [
                'form' => $spreadForm->createView()
            ]);
    }

    /**
     * @param Request $request
     * @param DeputyNoteRepository $deputyNoteRepository
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @Route("/export", name="deputy_note_export", methods={"GET", "POST"})
     */
    public function export(Request $request, DeputyNoteRepository $deputyNoteRepository) {

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('10- 3 Notes des députés nat');
        $notes = $deputyNoteRepository->findAll();
        $writer = new Xlsx($spreadsheet);

        $sheet->setCellValue('A1', 'Nom')
            ->setCellValue('B1', 'Prénom')
            ->setCellValue('C1', 'Adresse Ip')
            ->setCellValue('D1', 'Date d\'évaluation')
            ->setCellValue('E1', 'Nombre de présence physique en séance publique depuis le début de son mandat')
            ->setCellValue('F1', 'Nombre d\'Amendements depuis le début de son mandat')
            ->setCellValue('G1', 'Nombre de votes depuis le début de son mandat')
            ->setCellValue('H1', 'Nombre de participation depuis le début de son mandat, aux travaux parlementaires dans les commissions: permanentes, affaires européennes, spéciales, d\'apuration des comptes, mixtes paritaires, chargée de mettre en œuvre l\'art 26 de la constitution, d\'enq')
            ->setCellValue('I1', 'Nombre de propositions de loi déposé depuis le début de son mandat')
            ->setCellValue('J1', 'Nombre de rapports législatifs depuis le début de son mandat')
            ->setCellValue('K1', 'Nombre de questions écrites et orales depuis le début de son mandat')
            ->setCellValue('L1', 'Libellé de la commune')
            ->setCellValue('M1', 'Libellé de département')
            ->setCellValue('N1', 'Libellé de la région');

        foreach ($notes as $key => $note) {
            $row = $key + 2;
            $sheet->setCellValue("A{$row}", $note->getDeputy()->getLastName());
            $sheet->setCellValue("B$row", $note->getDeputy()->getFirstName());
            $sheet->setCellValue("C$row", $note->getIpAddress());
            $sheet->setCellValue("D$row", $note->getEvaluationDate());
            $sheet->setCellValue("E$row", $note->getPresenceNumber());
            $sheet->setCellValue("F$row", $note->getAmendmentsNumber());
            $sheet->setCellValue("G$row", $note->getVotesNumber());
            $sheet->setCellValue("H$row", $note->getParticipationsNumber());
            $sheet->setCellValue("I$row", $note->getSuggestionsNumber());
            $sheet->setCellValue("J$row", $note->getReportsNumber());
            $sheet->setCellValue("K$row", $note->getQuestionsNumber());
            $sheet->setCellValue("L$row", '');
            $sheet->setCellValue("M$row", $note->getDeputy()->getDepartmentLabel());
            $sheet->setCellValue("N$row", $note->getDeputy()->getAreaLabel());
        }
        $fileName = 'notes-deputes.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);

        $spreadsheet->disconnectWorksheets();

        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/{id}", name="deputy_note_show", methods={"GET"})
     */
    public function show(DeputyNote $deputyNote): Response
    {
        return $this->render("{$this->templateDir}show.html.twig", [
            'deputy_note' => $deputyNote,
        ]);
    }


    /**
     * @Route("/{id}", name="deputy_note_delete", methods={"DELETE"})
     */
    public function delete(Request $request, DeputyNote $deputyNote): Response
    {
        if ($this->isCsrfTokenValid('delete'.$deputyNote->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($deputyNote);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectuée avec succès.');
        }

        return $this->redirectToRoute('deputy_note_index');
    }
}
