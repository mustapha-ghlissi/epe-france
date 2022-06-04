<?php

namespace App\Controller\Manager;


use App\Entity\DepartmentalAdvisor;
use App\Entity\Mayor;
use App\Entity\MPDPRNote;
use App\Entity\RegionalAdvisor;
use App\Form\SpreadSheetType;
use App\Repository\CommunityAdvisorRepository;
use App\Repository\CorsicanAdvisorRepository;
use App\Repository\DepartmentalAdvisorRepository;
use App\Repository\MayorRepository;
use App\Repository\MPDPRNoteRepository;
use App\Repository\MunicipalAdvisorRepository;
use App\Repository\RegionalAdvisorRepository;
use App\Repository\SenatorRepository;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Carbon\Carbon;

/**
 * @Route("/manager/note/m/p/d/p/r")
 */
class MPDPRNoteController extends AbstractController
{
    private $templateDir = 'manager/mpdpr_note/';
    /**
     * @Route("/", name="m_p_d_p_r_note_index", methods={"GET"})
     */
    public function index(Request $request,
                          PaginatorInterface $paginator,
                          SerializerInterface $serializer,
                          MPDPRNoteRepository $mPDPRNoteRepository): Response
    {

        if($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $search = $request->query->get('search')['value'];
            $dql = "SELECT n FROM App\Entity\MPDPRNote n LEFT JOIN n.mayor m LEFT JOIN n.departmentalPresident d LEFT JOIN n.regionalPresident r ";

            if(!empty($search)) {
                $dql .= "where n.ipAddress like '%" . $search . "%'";
                $dql .= "OR n.evaluationDate like '%" . $search . "%'";
                $dql .= "OR m.firstName like '%" . $search . "%'";
                $dql .= "OR m.lastName like '%" . $search . "%'";

                $dql .= "OR d.firstName like '%" . $search . "%'";
                $dql .= "OR d.lastName like '%" . $search . "%'";

                $dql .= "OR r.firstName like '%" . $search . "%'";
                $dql .= "OR r.lastName like '%" . $search . "%'";
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
                $dql .= " ORDER BY m.firstName, d.firstName, r.firstName ";
            }
            else {
                $dql .= " ORDER BY m.lastName, d.lastName, r.lastName ";
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
            'm_p_d_p_r_notes' => $mPDPRNoteRepository->getCount(),
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
     * @param SenatorRepository $senatorRepository
     * @param DepartmentalAdvisorRepository $departmentalAdvisorRepository
     * @param RegionalAdvisorRepository $regionalAdvisorRepository
     * @param MunicipalAdvisorRepository $municipalAdvisorRepository
     * @param CorsicanAdvisorRepository $corsicanAdvisorRepository
     * @param CommunityAdvisorRepository $communityAdvisorRepository
     * @return RedirectResponse|Response
     * @throws Exception
     * @throws NonUniqueResultException
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @Route("/import", name="mdpdr_note_import", methods={"GET", "POST"})
     */
    public function import(Request $request,
                           MPDPRNoteRepository $MPDPRNoteRepository,
                           MayorRepository $mayorRepository,
                           DepartmentalAdvisorRepository $departmentalAdvisorRepository,
                           RegionalAdvisorRepository $regionalAdvisorRepository) {

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
            $sheetName = '10-1 Notes des M; PD; PR';
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

                $mayor = $mayorRepository->getByName([
                    'lastName' => '%' . $lastName . '%',
                    'firstName' => '%' . $firstName . '%'
                ]);
                $mPDPRNote = new MPDPRNote();
                $mPDPRNote
                    ->setIpAddress($sheet->getCell('C' . $i)->getValue())
                    ->setEvaluationDate($this->getFieldValue($sheet->getCell('D' . $i)->getFormattedValue(), null, true))
                    ->setSecurity(filter_var($sheet->getCell('E' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                    ->setSocialAction(filter_var($sheet->getCell('F' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                    ->setJobProfessionalInsert(filter_var($sheet->getCell('G' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                    ->setTeaching(filter_var($sheet->getCell('H' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                    ->setYouthChildhood(filter_var($sheet->getCell('I' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                    ->setSports(filter_var($sheet->getCell('J' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                    ->setEconomicalIntervention(filter_var($sheet->getCell('K' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                    ->setCityPolitics(filter_var($sheet->getCell('L' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                    ->setRuralDevelopment(filter_var($sheet->getCell('M' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                    ->setAccommodation(filter_var($sheet->getCell('N' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                    ->setEnvironment(filter_var($sheet->getCell('O' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                    ->setGarbage(filter_var($sheet->getCell('P' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                    ->setTelecoms(filter_var($sheet->getCell('Q' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                    ->setEnergy(filter_var($sheet->getCell('R' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                    ->setTransports(filter_var($sheet->getCell('S' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT));

                if($mayor instanceof Mayor) {
                    $nbImportedNote ++;
                    $mPDPRNote->setMayor($mayor);
                    $em->persist($mPDPRNote);
                }
                else {
                    $depPresident = $departmentalAdvisorRepository->getPresidentByName([
                        'lastName' => '%' . $lastName . '%',
                        'firstName' => '%' . $firstName . '%',
                        'functionLabel' => 'Président du conseil départemental'
                    ]);
                    if($depPresident instanceof DepartmentalAdvisor) {
                        $nbImportedNote ++;
                        $mPDPRNote->setDepartmentalPresident($depPresident);
                        $em->persist($mPDPRNote);
                    }
                    else {
                        $regPresident = $regionalAdvisorRepository->getPresidentByName([
                            'lastName' => '%' . $lastName . '%',
                            'firstName' => '%' . $firstName . '%',
                            'functionLabel' => 'Président du conseil régional'
                        ]);
                        if($regPresident instanceof RegionalAdvisor) {
                            $nbImportedNote ++;
                            $mPDPRNote->setRegionalPresident($regPresident);
                            $em->persist($mPDPRNote);
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
     * @param MPDPRNoteRepository $MPDPRNoteRepository
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @Route("/export", name="mdpdr_note_export", methods={"GET", "POST"})
     */
    public function export(Request $request, MPDPRNoteRepository  $MPDPRNoteRepository) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('10- 4 Notes des députés Europ');
        $notes = $MPDPRNoteRepository->findAll();
        $writer = new Xlsx($spreadsheet);

        $sheet->setCellValue('A1', 'Nom')
            ->setCellValue('B1', 'Prénom')
            ->setCellValue('C1', 'Adresse Ip')
            ->setCellValue('D1', 'Date d\'évaluation')
            ->setCellValue('E1', 'Sécurité')
            ->setCellValue('F1', 'Action sociale et santé')
            ->setCellValue('G1', 'Emploi – Insertion professionnelle')
            ->setCellValue('H1', 'Enseignement')
            ->setCellValue('I1', 'Enfance -  Jeunesse')
            ->setCellValue('J1', 'Sports')
            ->setCellValue('K1', 'Interventions dans le domaine économique')
            ->setCellValue('L1', 'Politique de la ville')
            ->setCellValue('M1', 'Aménagement rural, planification et aménagement du territoire ')
            ->setCellValue('N1', 'Logement et habitat')
            ->setCellValue('O1', 'Environnement et patrimoine')
            ->setCellValue('P1', 'Déchets')
            ->setCellValue('Q1', 'Réseaux caclés et télécommunications')
            ->setCellValue('R1', 'Energie')
            ->setCellValue('S1', 'Transports scolaires et publics')
            ->setCellValue('T1', 'Libellé de la commune')
            ->setCellValue('U1', 'Libellé de la département')
            ->setCellValue('V1', 'Libellé de la région');

        foreach ($notes as $key => $note) {
            $row = $key + 2;

            if($note->getMayor()) {
                $sheet->setCellValue("A{$row}", $note->getMayor()->getLastName())
                    ->setCellValue("B$row", $note->getMayor()->getFirstName())
                    ->setCellValue("T$row", $note->getMayor()->getCommuneLabel())
                    ->setCellValue("U$row", $note->getMayor()->getDepartmentLabel())
                    ->setCellValue("V$row", $note->getMayor()->getAreaLabel());

            }
            elseif ($note->getDepartmentalPresident()) {
                $sheet->setCellValue("A{$row}", $note->getDepartmentalPresident()->getLastName())
                    ->setCellValue("B$row", $note->getDepartmentalPresident()->getFirstName())
                    ->setCellValue("T$row", '')
                    ->setCellValue("U$row", $note->getDepartmentalPresident()->getDepartmentLabel())
                    ->setCellValue("V$row", $note->getDepartmentalPresident()->getAreaLabel());
            }
            else {
                $sheet->setCellValue("A{$row}", $note->getRegionalPresident()->getLastName())
                    ->setCellValue("B{$row}", $note->getRegionalPresident()->getFirstName())
                    ->setCellValue("T{$row}", '')
                    ->setCellValue("U{$row}", $note->getRegionalPresident()->getDepartmentLabel())
                    ->setCellValue("V$row", $note->getRegionalPresident()->getAreaLabel());
            }

            $sheet->setCellValue("C$row", $note->getIpAddress())
                ->setCellValue("D$row", $note->getEvaluationDate())
                ->setCellValue("E$row", $note->getSecurity())
                ->setCellValue("F$row", $note->getSocialAction())
                ->setCellValue("G$row", $note->getJobProfessionalInsert())
                ->setCellValue("H$row", $note->getTeaching())
                ->setCellValue("I$row", $note->getYouthChildhood())
                ->setCellValue("J$row", $note->getSports())
                ->setCellValue("K$row", $note->getEconomicalIntervention())
                ->setCellValue("L$row", $note->getCityPolitics())
                ->setCellValue("M$row", $note->getRuralDevelopment())
                ->setCellValue("N$row", $note->getAccommodation())
                ->setCellValue("O$row", $note->getEnvironment())
                ->setCellValue("P$row", $note->getGarbage())
                ->setCellValue("Q$row", $note->getTelecoms())
                ->setCellValue("R$row", $note->getEnergy())
                ->setCellValue("S$row", $note->getTransports());
        }
        $fileName = 'mpdpr-notes.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);

        $spreadsheet->disconnectWorksheets();

        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/{id}", name="m_p_d_p_r_note_show", methods={"GET"})
     */
    public function show(MPDPRNote $mPDPRNote): Response
    {
        return $this->render("{$this->templateDir}show.html.twig", [
            'm_p_d_p_r_note' => $mPDPRNote,
        ]);
    }

    /**
     * @Route("/{id}", name="m_p_d_p_r_note_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MPDPRNote $mPDPRNote): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mPDPRNote->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mPDPRNote);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectuée avec succès.');
        }

        return $this->redirectToRoute('m_p_d_p_r_note_index');
    }
}
