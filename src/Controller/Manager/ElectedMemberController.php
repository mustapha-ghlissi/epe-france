<?php

namespace App\Controller\Manager;

use App\Entity\CommunityAdvisor;
use App\Entity\CorsicanAdvisor;
use App\Entity\DepartmentalAdvisor;
use App\Entity\Deputy;
use App\Entity\EuroDeputy;
use App\Entity\ExtraData;
use App\Entity\Mayor;
use App\Entity\MunicipalAdvisor;
use App\Entity\RegionalAdvisor;
use App\Entity\Senator;
use App\Form\SpreadSheetType;
use App\Repository\CommunityAdvisorRepository;
use App\Repository\CorsicanAdvisorRepository;
use App\Repository\DepartmentalAdvisorRepository;
use App\Repository\DeputyRepository;
use App\Repository\EuroDeputyRepository;
use App\Repository\ExtraDataRepository;
use App\Repository\MayorRepository;
use App\Repository\MunicipalAdvisorRepository;
use App\Repository\RegionalAdvisorRepository;
use App\Repository\SenatorRepository;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ElectedMemberController
 * @package App\Controller\Admin
 * @Route("/manager/extra-data/category")
 */
class ElectedMemberController extends AbstractController
{
    /**
     * @param int $categoryId
     * @param int $id
     * @param Request $request
     * @param MunicipalAdvisorRepository $municipalAdvisorRepository
     * @param CommunityAdvisorRepository $communityAdvisorRepository
     * @param DepartmentalAdvisorRepository $departmentalAdvisorRepository
     * @param RegionalAdvisorRepository $regionalAdvisorRepository
     * @param CorsicanAdvisorRepository $corsicanAdvisorRepository
     * @param EuroDeputyRepository $euroDeputyRepository
     * @param DeputyRepository $deputyRepository
     * @param MayorRepository $mayorRepository
     * @param SenatorRepository $senatorRepository
     * @return Response
     * @Route("/{categoryId}/elected-member/{id}", name="manager_elected_member")
     */
    public function index(int $categoryId, int $id,
                          Request $request,
                          ExtraDataRepository $extraDataRepository,
                          MunicipalAdvisorRepository $municipalAdvisorRepository,
                          CommunityAdvisorRepository $communityAdvisorRepository,
                          DepartmentalAdvisorRepository $departmentalAdvisorRepository,
                          RegionalAdvisorRepository  $regionalAdvisorRepository,
                          CorsicanAdvisorRepository $corsicanAdvisorRepository,
                          EuroDeputyRepository $euroDeputyRepository,
                          DeputyRepository $deputyRepository,
                          MayorRepository $mayorRepository,
                          SenatorRepository $senatorRepository)
    {
        $electedMember = $this->getElectedMember(
            $categoryId,
            $id,
            $municipalAdvisorRepository,
            $communityAdvisorRepository,
            $departmentalAdvisorRepository,
            $regionalAdvisorRepository,
            $corsicanAdvisorRepository,
            $euroDeputyRepository,
            $deputyRepository,
            $mayorRepository,
            $senatorRepository
        );

        if(!is_object($electedMember)) {
            throw $this->createNotFoundException('Elu introuvable.');
        }

        $extraData = $this->getExtraDataByElectedMember($extraDataRepository, $electedMember);
        if($request->isMethod(Request::METHOD_POST)) {
            $em = $this->getDoctrine()->getManager();

            if($extraData instanceof ExtraData) {
                //Update the extra data (Video embed, widgets, descriptions)
                $extraData->setSocialTimelines($request->request->get('timelineSourceCode'));
                $videos['links'] = $request->request->get('videoLink');
                $videos['descriptions'] = $request->request->get('videoDescription');
                $extraData->setVideos($videos);
                $em->flush();
                $this->addFlash('success', 'Mis à jour avec succès');
            }
            else {
                // Create new
                $extraData = new ExtraData();
                $extraData->setSocialTimelines($request->request->get('timelineSourceCode'));
                $videos['links'] = $request->request->get('videoLink');
                $videos['descriptions'] = $request->request->get('videoDescription');
                $extraData->setVideos($videos);
                $extraData = $this->setElectedMember($extraData, $electedMember);
                $em->persist($extraData);
                $em->flush();
                $this->addFlash('success', 'Enregistré avec succès');
            }

            return $this->redirectToRoute('manager_elected_member', ['categoryId' => $categoryId, 'id' => $id]);
        }

        return $this->render('manager/elected_member/index.html.twig', [
            'electedMember' => $electedMember,
            'categoryId' => $categoryId
        ]);
    }

    /**
     * @param int $categoryId
     * @param int $id
     * @param ExtraData $extraData
     * @param Request $request
     * @return RedirectResponse
     * @Route("/{categoryId}/elected-member/{memberId}/{id}", name="manager_delete_extra_data", methods={"DELETE"})
     */
    public function delete(int $categoryId, int $memberId, ExtraData $extraData, Request $request) {
        if ($this->isCsrfTokenValid('delete'.$extraData->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($extraData);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectée avec succès');
        }

        return $this->redirectToRoute('manager_elected_member', ['categoryId' => $categoryId, 'id' => $memberId]);
    }


    /**
     * @param int $categoryId
     * @param int $id
     * @param MunicipalAdvisorRepository $municipalAdvisorRepository
     * @param CommunityAdvisorRepository $communityAdvisorRepository
     * @param DepartmentalAdvisorRepository $departmentalAdvisorRepository
     * @param RegionalAdvisorRepository $regionalAdvisorRepository
     * @param CorsicanAdvisorRepository $corsicanAdvisorRepository
     * @param EuroDeputyRepository $euroDeputyRepository
     * @param DeputyRepository $deputyRepository
     * @param MayorRepository $mayorRepository
     * @param SenatorRepository $senatorRepository
     * @return CommunityAdvisor|CorsicanAdvisor|DepartmentalAdvisor|Deputy|EuroDeputy|Mayor|MunicipalAdvisor|RegionalAdvisor|Senator|null
     */
    private function getElectedMember(int $categoryId, int $id, MunicipalAdvisorRepository $municipalAdvisorRepository,
                                     CommunityAdvisorRepository $communityAdvisorRepository,
                                     DepartmentalAdvisorRepository $departmentalAdvisorRepository,
                                     RegionalAdvisorRepository  $regionalAdvisorRepository,
                                     CorsicanAdvisorRepository $corsicanAdvisorRepository,
                                     EuroDeputyRepository $euroDeputyRepository,
                                     DeputyRepository $deputyRepository,
                                     MayorRepository $mayorRepository,
                                     SenatorRepository $senatorRepository) {
        switch ($categoryId) {
            case 1:
                $electedMember = $municipalAdvisorRepository->find($id);
                break;
            case 2:
                $electedMember = $communityAdvisorRepository->find($id);
                break;
            case 3:
                $electedMember = $departmentalAdvisorRepository->find($id);
                break;
            case 4:
                $electedMember = $regionalAdvisorRepository->find($id);
                break;
            case 5:
                $electedMember = $corsicanAdvisorRepository->find($id);
                break;
            case 6:
                $electedMember = $euroDeputyRepository->find($id);
                break;
            case 7:
                $electedMember = $senatorRepository->find($id);
                break;
            case 8:
                $electedMember = $deputyRepository->find($id);
                break;
            case 9:
                $electedMember = $mayorRepository->find($id);
                break;
        }

        return $electedMember ?? null;
    }

    /**
     * @param ExtraData $extraData
     * @param $electedMember
     * @return ExtraData
     */
    public function setElectedMember(ExtraData $extraData, $electedMember): ExtraData {

        switch (get_class($electedMember)) {
            case 'App\Entity\Mayor':
                $extraData->setMayor($electedMember);
                break;
            case 'App\Entity\CommunityAdvisor':
                $extraData->setCommunityAdvisor($electedMember);
                break;
            case 'App\Entity\CorsicanAdvisor':
                $extraData->setCorsicanAdvisor($electedMember);
                break;
            case 'App\Entity\DepartmentalAdvisor':
                $extraData->setDepartmentalAdvisor($electedMember);
                break;
            case 'App\Entity\Deputy':
                $extraData->setDeputy($electedMember);
                break;
            case 'App\Entity\EuroDeputy':
                $extraData->setEuroDeputy($electedMember);
                break;
            case 'App\Entity\MunicipalAdvisor':
                $extraData->setMunicipalAdvisor($electedMember);
                break;
            case 'App\Entity\RegionalAdvisor':
                $extraData->setRegionalAdvisor($electedMember);
                break;
            case 'App\Entity\Senator':
                $extraData->setSenator($electedMember);
                break;
            default:
                break;
        }

        return $extraData;
    }

    /**
     * @param ExtraDataRepository $extraDataRepository
     * @param $electedMember
     * @return ExtraData|null
     */
    private function getExtraDataByElectedMember(ExtraDataRepository  $extraDataRepository, $electedMember) {

        switch (get_class($electedMember)) {
            case 'App\Entity\Mayor':
                $extraData = $extraDataRepository->findOneByMayor($electedMember->getId());
                break;
            case 'App\Entity\CommunityAdvisor':
                $extraData = $extraDataRepository->findOneByCommunityAdvisor($electedMember->getId());
                break;
            case 'App\Entity\CorsicanAdvisor':
                $extraData = $extraDataRepository->findOneByCorsicanAdvisor($electedMember->getId());
                break;
            case 'App\Entity\DepartmentalAdvisor':
                $extraData = $extraDataRepository->findOneByDepartmentalAdvisor($electedMember->getId());
                break;
            case 'App\Entity\Deputy':
                $extraData = $extraDataRepository->findOneByDeputy($electedMember->getId());
                break;
            case 'App\Entity\EuroDeputy':
                $extraData = $extraDataRepository->findOneByEuroDeputy($electedMember->getId());
                break;
            case 'App\Entity\MunicipalAdvisor':
                $extraData = $extraDataRepository->findOneByMunicipalAdvisor($electedMember->getId());
                break;
            case 'App\Entity\RegionalAdvisor':
                $extraData = $extraDataRepository->findOneByRegionalAdvisor($electedMember->getId());
                break;
            case 'App\Entity\Senator':
                $extraData = $extraDataRepository->findOneBySenator($electedMember->getId());
                break;
            default:
                break;
        }
        return $extraData ?? null;
    }

    private function getFieldValue($value, $filter = null, $isDate = false, $source = null) {
        if(trim($value) !== '') {
            if($filter) {
                return filter_var($value, $filter);
            }
            elseif($isDate) {
                return new Carbon($value);
            }
            elseif($source) {
                return $this->extractCode($value, $source);
            }
            return $value;
        }
        return null;
    }

    /**
     * @param int $categoryId
     * @param Request $request
     * @param MunicipalAdvisorRepository $municipalAdvisorRepository
     * @param CommunityAdvisorRepository $communityAdvisorRepository
     * @param DepartmentalAdvisorRepository $departmentalAdvisorRepository
     * @param RegionalAdvisorRepository $regionalAdvisorRepository
     * @param CorsicanAdvisorRepository $corsicanAdvisorRepository
     * @param EuroDeputyRepository $euroDeputyRepository
     * @param SenatorRepository $senatorRepository
     * @param DeputyRepository $deputyRepository
     * @param MayorRepository $mayorRepository
     * @return Response
     * @throws Exception
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @Route("/{categoryId}/spreadsheet/import", name="import_spreadsheet", methods={"GET", "POST"}, requirements={"categoryId"="\d+"})
     */
    public function importSpreadsheet(int $categoryId, Request  $request,
                                      MunicipalAdvisorRepository  $municipalAdvisorRepository,
                                      CommunityAdvisorRepository  $communityAdvisorRepository,
                                      DepartmentalAdvisorRepository  $departmentalAdvisorRepository,
                                      RegionalAdvisorRepository  $regionalAdvisorRepository,
                                      CorsicanAdvisorRepository $corsicanAdvisorRepository,
                                      EuroDeputyRepository $euroDeputyRepository,
                                      SenatorRepository $senatorRepository,
                                      DeputyRepository $deputyRepository, MayorRepository $mayorRepository): Response
    {
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
            if($categoryId === 1) {
                $referer = $this->generateUrl('municipal_advisor_index');
                $sheetName = '01- Conseillers municipaux';
                $inputFileType = IOFactory::identify($spreadsheetFile);
                $reader = IOFactory::createReader($inputFileType);
                $reader->setLoadSheetsOnly($sheetName);
                $spreadsheet = $reader->load($spreadsheetFile);
                $sheet = $spreadsheet->getActiveSheet();
                $sheetRows = $sheet->getHighestRow();

                for($i = 2; $i <= $sheetRows; $i++) {
                    $municipalAdvisor = new MunicipalAdvisor();
                    $municipalAdvisor
                        ->setDepartmentCode($this->getFieldValue($sheet->getCell('A' . $i)->getValue(), null, false, 'department'))
                        ->setDepartmentLabel($this->getFieldValue($sheet->getCell('B' . $i)->getValue()))
                        ->setDepartmentCapital($this->getFieldValue($sheet->getCell('C' . $i)->getValue()))
                        ->setDepartmentPopulation($this->getFieldValue($sheet->getCell('D' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setDepartmentSurface($this->getFieldValue($sheet->getCell('E' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setDepartmentDensity($this->getFieldValue($sheet->getCell('F' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setAreaLabel($this->getFieldValue($sheet->getCell('G' . $i)->getValue()))
                        ->setNbDepartments($this->getFieldValue($sheet->getCell('H' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaCapital($this->getFieldValue($sheet->getCell('I' . $i)->getValue()))
                        ->setAreaSurface($this->getFieldValue($sheet->getCell('J' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaPopulation($this->getFieldValue($sheet->getCell('K' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaDensity($this->getFieldValue($sheet->getCell('L' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setCodeInsee($this->getFieldValue($sheet->getCell('M' . $i)->getValue(), null, false, 'commune'))
                        ->setCommuneLabel($this->getFieldValue($sheet->getCell('N' . $i)->getValue()))
                        ->setLastName($this->getFieldValue($sheet->getCell('O' . $i)->getValue()))
                        ->setFirstName($this->getFieldValue($sheet->getCell('P' . $i)->getValue()))
                        ->setGender($this->getFieldValue($sheet->getCell('Q' . $i)->getValue()))
                        ->setBirthDate($this->getFieldValue($sheet->getCell('R' . $i)->getFormattedValue(), null, true))
                        ->setProfessionCode($this->getFieldValue($sheet->getCell('S' . $i)->getValue()))
                        ->setProfessionLabel($this->getFieldValue($sheet->getCell('T' . $i)->getValue()))
                        ->setMandateStartDate($this->getFieldValue($sheet->getCell('U' . $i)->getFormattedValue(), null, true))
                        ->setFunctionLabel($this->getFieldValue($sheet->getCell('V' . $i)->getValue()))
                        ->setFunctionStartDate($this->getFieldValue($sheet->getCell('W' . $i)->getFormattedValue(), null, true))
                        ->setNationality($this->getFieldValue($sheet->getCell('X' . $i)->getValue()))
                        ->setZipCode($this->getFieldValue($sheet->getCell('Y' . $i)->getValue(), null, false, 'commune'))
                        ->setPopulationYear($this->getFieldValue($sheet->getCell('Z' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setNbPublicService($this->getFieldValue($sheet->getCell('AA' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setLatitude($this->getFieldValue($sheet->getCell('AB' . $i)->getValue()))
                        ->setLongitude($this->getFieldValue($sheet->getCell('AC' . $i)->getValue()));
                    $em->persist($municipalAdvisor);
                }
            }
            elseif($categoryId === 2) {
                $referer = $this->generateUrl('community_advisor_index');
                $sheetName = '02- Conseillers communautaires';
                $inputFileType = IOFactory::identify($spreadsheetFile);
                $reader = IOFactory::createReader($inputFileType);
                $reader->setLoadSheetsOnly($sheetName);
                $spreadsheet = $reader->load($spreadsheetFile);
                $sheet = $spreadsheet->getActiveSheet();
                $sheetRows = $sheet->getHighestRow();
                for($i = 2; $i <= $sheetRows; $i++) {
                    $communityAdvisor = new CommunityAdvisor();
                    $communityAdvisor
                        ->setEpciDepartmentCode($this->getFieldValue($sheet->getCell('A' . $i)->getValue(), null, false, 'department'))
                        ->setSiren($this->getFieldValue($sheet->getCell('B' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setEpciLabel($this->getFieldValue($sheet->getCell('C' . $i)->getValue()))
                        ->setCommuneCode($this->getFieldValue($sheet->getCell('D' . $i)->getValue(), null, false, 'commune'))
                        ->setDepartmentCode($this->getFieldValue($sheet->getCell('E' . $i)->getValue(), null, false, 'department'))
                        ->setDepartmentLabel($this->getFieldValue($sheet->getCell('F' . $i)->getValue()))
                        ->setDepartmentCapital($this->getFieldValue($sheet->getCell('G' . $i)->getValue()))
                        ->setDepartmentPopulation($this->getFieldValue($sheet->getCell('H' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setDepartmentSurface($this->getFieldValue($sheet->getCell('I' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setDepartmentDensity($this->getFieldValue($sheet->getCell('J' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setAreaLabel($this->getFieldValue($sheet->getCell('K' . $i)->getValue()))
                        ->setNbDepartments($this->getFieldValue($sheet->getCell('L' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaCapital($this->getFieldValue($sheet->getCell('M' . $i)->getValue()))
                        ->setAreaSurface($this->getFieldValue($sheet->getCell('N' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaPopulation($this->getFieldValue($sheet->getCell('O' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaDensity($this->getFieldValue($sheet->getCell('P' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setCommuneLabel($this->getFieldValue($sheet->getCell('Q' . $i)->getValue()))
                        ->setLastName($this->getFieldValue($sheet->getCell('R' . $i)->getValue()))
                        ->setFirstName($this->getFieldValue($sheet->getCell('S' . $i)->getValue()))
                        ->setGender($this->getFieldValue($sheet->getCell('T' . $i)->getValue()))
                        ->setBirthDate($this->getFieldValue($sheet->getCell('U' . $i)->getFormattedValue(), null, true))
                        ->setProfessionCode($this->getFieldValue($sheet->getCell('V' . $i)->getValue()))
                        ->setProfessionLabel($this->getFieldValue($sheet->getCell('W' . $i)->getValue()))
                        ->setMandateStartDate($this->getFieldValue($sheet->getCell('X' . $i)->getFormattedValue(), null, true))
                        ->setFunctionLabel($this->getFieldValue($sheet->getCell('Y' . $i)->getValue()))
                        ->setFunctionStartDate($this->getFieldValue($sheet->getCell('Z' . $i)->getFormattedValue(), null, true))
                        ->setZipCode($this->getFieldValue($sheet->getCell('AA' . $i)->getValue(), null, false, 'commune'))
                        ->setPopulationYear($this->getFieldValue($sheet->getCell('AB' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setNbPublicService($this->getFieldValue($sheet->getCell('AC' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setLatitude($this->getFieldValue($sheet->getCell('AD' . $i)->getValue()))
                        ->setLongitude($this->getFieldValue($sheet->getCell('AE' . $i)->getValue()))
                    ;
                    $em->persist($communityAdvisor);
                }
            }
            elseif($categoryId === 3) {
                $referer = $this->generateUrl('departmental_advisor_index');
                $sheetName = '03- Conseillers départementaux';
                $inputFileType = IOFactory::identify($spreadsheetFile);
                $reader = IOFactory::createReader($inputFileType);
                $reader->setLoadSheetsOnly($sheetName);
                $spreadsheet = $reader->load($spreadsheetFile);
                $sheet = $spreadsheet->getActiveSheet();
                $sheetRows = $sheet->getHighestRow();
                for($i = 2; $i <= $sheetRows; $i++) {
                    $departmentalAdvisor = new DepartmentalAdvisor();
                    $departmentalAdvisor
                        ->setDepartmentCode($this->getFieldValue($sheet->getCell('A' . $i)->getValue(), null, false, 'department'))
                        ->setDepartmentLabel($this->getFieldValue($sheet->getCell('B' . $i)->getValue()))
                        ->setDepartmentCapital($sheet->getCell('C' . $i)->getValue())
                        ->setDepartmentPopulation($this->getFieldValue($sheet->getCell('D' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setDepartmentSurface($this->getFieldValue($sheet->getCell('E' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setDepartmentDensity($this->getFieldValue($sheet->getCell('F' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setAreaLabel($this->getFieldValue($sheet->getCell('G' . $i)->getValue()))
                        ->setNbDepartments($this->getFieldValue($sheet->getCell('H' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaCapital($this->getFieldValue($sheet->getCell('I' . $i)->getValue()))
                        ->setAreaSurface($this->getFieldValue($sheet->getCell('J' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaPopulation($this->getFieldValue($sheet->getCell('K' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaDensity($this->getFieldValue($sheet->getCell('L' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setLastName($this->getFieldValue($sheet->getCell('M' . $i)->getValue()))
                        ->setFirstName($this->getFieldValue($sheet->getCell('N' . $i)->getValue()))
                        ->setGender($this->getFieldValue($sheet->getCell('O' . $i)->getValue()))
                        ->setCantonCode($this->getFieldValue($sheet->getCell('P' . $i)->getValue()))
                        ->setCantonLabel($this->getFieldValue($sheet->getCell('Q' . $i)->getValue()))
                        ->setFunctionLabel($this->getFieldValue($sheet->getCell('R' . $i)->getValue()))
                        ->setProfessionCode($this->getFieldValue($sheet->getCell('S' . $i)->getValue()))
                        ->setProfessionLabel($this->getFieldValue($sheet->getCell('T' . $i)->getValue()))
                        ->setBirthDate($this->getFieldValue($sheet->getCell('U' . $i)->getFormattedValue(), null, true))
                        ->setMandateStartDate($this->getFieldValue($sheet->getCell('V' . $i)->getFormattedValue(), null, true))
                        ->setFunctionStartDate($this->getFieldValue($sheet->getCell('W' . $i)->getFormattedValue(), null, true))
                        ->setPopulationYear($this->getFieldValue($sheet->getCell('X' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setNbPublicService($this->getFieldValue($sheet->getCell('Y' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT));

                    $departmentalAdvisor->setLatitude($this->getFieldValue($sheet->getCell('Z' . $i)->getValue()));
                    $departmentalAdvisor->setLongitude($this->getFieldValue($sheet->getCell('AA' . $i)->getValue()));
                    $em->persist($departmentalAdvisor);
                }
            }
            elseif($categoryId === 4) {
                $referer = $this->generateUrl('regional_advisor_index');
                $sheetName = '04- Conseillers régionaux';
                $inputFileType = IOFactory::identify($spreadsheetFile);
                $reader = IOFactory::createReader($inputFileType);
                $reader->setLoadSheetsOnly($sheetName);
                $spreadsheet = $reader->load($spreadsheetFile);
                $sheet = $spreadsheet->getActiveSheet();
                $sheetRows = $sheet->getHighestRow();
                for($i = 2; $i <= $sheetRows; $i++) {
                    $regionalAdvisor = new RegionalAdvisor();
                    $regionalAdvisor
                        ->setRegionCode($this->getFieldValue($sheet->getCell('A' . $i)->getValue(), null, false, 'region'))
                        ->setRegionLabel($this->getFieldValue($sheet->getCell('B' . $i)->getValue()))
                        ->setDepartmentCode($this->getFieldValue($sheet->getCell('C' . $i)->getValue(), null, false, 'department'))
                        ->setDepartmentLabel($this->getFieldValue($sheet->getCell('D' . $i)->getValue()))
                        ->setDepartmentCapital($this->getFieldValue($sheet->getCell('E' . $i)->getValue()))
                        ->setDepartmentPopulation($this->getFieldValue($sheet->getCell('F' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setDepartmentSurface($this->getFieldValue($sheet->getCell('G' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setDepartmentDensity($this->getFieldValue($sheet->getCell('H' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setAreaLabel($this->getFieldValue($sheet->getCell('I' . $i)->getValue()))
                        ->setNbDepartments($this->getFieldValue($sheet->getCell('J' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaCapital($this->getFieldValue($sheet->getCell('K' . $i)->getValue()))
                        ->setAreaSurface($this->getFieldValue($sheet->getCell('L' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaPopulation($this->getFieldValue($sheet->getCell('M' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaDensity($this->getFieldValue($sheet->getCell('N' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setLastName($this->getFieldValue($sheet->getCell('O' . $i)->getValue()))
                        ->setFirstName($this->getFieldValue($sheet->getCell('P' . $i)->getValue()))
                        ->setGender($this->getFieldValue($sheet->getCell('Q' . $i)->getValue()))
                        ->setBirthDate($this->getFieldValue($sheet->getCell('R' . $i)->getFormattedValue(), null, true))
                        ->setProfessionCode($this->getFieldValue($sheet->getCell('S' . $i)->getValue()))
                        ->setProfessionLabel($this->getFieldValue($sheet->getCell('T' . $i)->getValue()))
                        ->setMandateStartDate($this->getFieldValue($sheet->getCell('U' . $i)->getFormattedValue(), null, true))
                        ->setFunctionLabel($this->getFieldValue($sheet->getCell('V' . $i)->getValue()))
                        ->setFunctionStartDate($this->getFieldValue($sheet->getCell('W' . $i)->getFormattedValue(), null, true))
                        ->setPopulationYear($this->getFieldValue($sheet->getCell('X' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setNbPublicService($this->getFieldValue($sheet->getCell('Y' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT));
                        $regionalAdvisor->setLatitude($this->getFieldValue($sheet->getCell('Z' . $i)->getValue()));
                        $regionalAdvisor->setLongitude($this->getFieldValue($sheet->getCell('AA' . $i)->getValue()));

                    $em->persist($regionalAdvisor);
                }
            }
            elseif($categoryId === 5) {
                $referer = $this->generateUrl('corsican_advisor_index');
                $sheetName = '05- Conseillers Corse';
                $inputFileType = IOFactory::identify($spreadsheetFile);
                $reader = IOFactory::createReader($inputFileType);
                $reader->setLoadSheetsOnly($sheetName);
                $spreadsheet = $reader->load($spreadsheetFile);
                $sheet = $spreadsheet->getActiveSheet();
                $sheetRows = $sheet->getHighestRow();
                for($i = 2; $i <= $sheetRows; $i++) {
                    $corsicanAdvisor = new CorsicanAdvisor();
                    $corsicanAdvisor
                        ->setDepartmentCode($this->getFieldValue($sheet->getCell('A' . $i)->getValue(), null, false, 'department'))
                        ->setDepartmentLabel($this->getFieldValue($sheet->getCell('B' . $i)->getValue()))
                        ->setDepartmentCapital($this->getFieldValue($sheet->getCell('C' . $i)->getValue()))
                        ->setDepartmentPopulation($this->getFieldValue($sheet->getCell('D' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setDepartmentSurface($this->getFieldValue($sheet->getCell('E' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setDepartmentDensity($this->getFieldValue($sheet->getCell('F' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setAreaLabel($this->getFieldValue($sheet->getCell('G' . $i)->getValue()))
                        ->setNbDepartments($this->getFieldValue($sheet->getCell('H' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaCapital($this->getFieldValue($sheet->getCell('I' . $i)->getValue()))
                        ->setAreaSurface($this->getFieldValue($sheet->getCell('J' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaPopulation($this->getFieldValue($sheet->getCell('K' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaDensity($this->getFieldValue($sheet->getCell('L' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setLastName($this->getFieldValue($sheet->getCell('M' . $i)->getValue()))
                        ->setFirstName($this->getFieldValue($sheet->getCell('N' . $i)->getValue()))
                        ->setGender($this->getFieldValue($sheet->getCell('O' . $i)->getValue()))
                        ->setBirthDate($this->getFieldValue($sheet->getCell('P' . $i)->getFormattedValue(), null, true))
                        ->setProfessionCode($this->getFieldValue($sheet->getCell('Q' . $i)->getValue()))
                        ->setProfessionLabel($this->getFieldValue($sheet->getCell('R' . $i)->getValue()))
                        ->setMandateStartDate($this->getFieldValue($sheet->getCell('S' . $i)->getFormattedValue(), null, true))
                        ->setFunctionLabel($this->getFieldValue($sheet->getCell('T' . $i)->getValue()))
                        ->setFunctionStartDate($this->getFieldValue($sheet->getCell('U' . $i)->getFormattedValue(), null, true))
                        ->setPopulationYear($this->getFieldValue($sheet->getCell('V' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setNbPublicService($this->getFieldValue($sheet->getCell('W' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT));

                        $corsicanAdvisor->setLatitude($this->getFieldValue($sheet->getCell('X' . $i)->getValue()));
                        $corsicanAdvisor->setLongitude($this->getFieldValue($sheet->getCell('Y' . $i)->getValue()))

                    ;
                    $em->persist($corsicanAdvisor);
                }
            }
            elseif($categoryId === 6) {
                $referer = $this->generateUrl('euro_deputy_index');
                $sheetName = '06- Députés Européens';
                $inputFileType = IOFactory::identify($spreadsheetFile);
                $reader = IOFactory::createReader($inputFileType);
                $reader->setLoadSheetsOnly($sheetName);
                $spreadsheet = $reader->load($spreadsheetFile);
                $sheet = $spreadsheet->getActiveSheet();
                $sheetRows = $sheet->getHighestRow();
                for($i = 2; $i <= $sheetRows; $i++) {
                    $euroDeputy = new EuroDeputy();
                    $euroDeputy->setLastName($this->getFieldValue($sheet->getCell('A' . $i)->getValue()))
                        ->setFirstName($this->getFieldValue($sheet->getCell('B' . $i)->getValue()))
                        ->setGender($this->getFieldValue($sheet->getCell('C' . $i)->getValue()))
                        ->setBirthDate($this->getFieldValue($sheet->getCell('D' . $i)->getFormattedValue(), null, true))
                        ->setProfessionCode($this->getFieldValue($sheet->getCell('E' . $i)->getValue()))
                        ->setProfessionLabel($this->getFieldValue($sheet->getCell('F' . $i)->getValue()))
                        ->setMandateStartDate($this->getFieldValue($sheet->getCell('G' . $i)->getFormattedValue(), null, true));
                    $em->persist($euroDeputy);
                }
            }
            elseif($categoryId === 7) {
                $referer = $this->generateUrl('senator_index');
                $sheetName = '07- Sénateurs';
                $inputFileType = IOFactory::identify($spreadsheetFile);
                $reader = IOFactory::createReader($inputFileType);
                $reader->setLoadSheetsOnly($sheetName);
                $spreadsheet = $reader->load($spreadsheetFile);
                $sheet = $spreadsheet->getActiveSheet();
                $sheetRows = $sheet->getHighestRow();
                for($i = 2; $i <= $sheetRows; $i++) {
                    $senator = new Senator();
                    $senator
                        ->setDepartmentCode($this->getFieldValue($sheet->getCell('A' . $i)->getValue(), null, false, 'department'))
                        ->setDepartmentLabel($this->getFieldValue($sheet->getCell('B' . $i)->getValue()))
                        ->setDepartmentCapital($this->getFieldValue($sheet->getCell('C' . $i)->getValue()))
                        ->setDepartmentPopulation($this->getFieldValue($sheet->getCell('D' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setDepartmentSurface($this->getFieldValue($sheet->getCell('E' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setDepartmentDensity($this->getFieldValue($sheet->getCell('F' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setAreaLabel($this->getFieldValue($sheet->getCell('G' . $i)->getValue()))
                        ->setNbDepartments($this->getFieldValue($sheet->getCell('H' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaCapital($this->getFieldValue($sheet->getCell('I' . $i)->getValue()))
                        ->setAreaSurface($this->getFieldValue($sheet->getCell('J' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaPopulation($this->getFieldValue($sheet->getCell('K' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaDensity($this->getFieldValue($sheet->getCell('L' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setLastName($this->getFieldValue($sheet->getCell('M' . $i)->getValue()))
                        ->setFirstName($this->getFieldValue($sheet->getCell('N' . $i)->getValue()))
                        ->setGender($this->getFieldValue($sheet->getCell('O' . $i)->getValue()))
                        ->setBirthDate($this->getFieldValue($sheet->getCell('P' . $i)->getFormattedValue(), null, true))
                        ->setProfessionCode($this->getFieldValue($sheet->getCell('Q' . $i)->getValue()))
                        ->setProfessionLabel($this->getFieldValue($sheet->getCell('R' . $i)->getValue()))
                        ->setMandateStartDate($this->getFieldValue($sheet->getCell('S' . $i)->getFormattedValue(), null, true))
                        ->setPopulationYear($this->getFieldValue($sheet->getCell('T' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setNbPublicService($this->getFieldValue($sheet->getCell('U' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT));
                        $senator->setLatitude($this->getFieldValue($sheet->getCell('V' . $i)->getValue()));
                        $senator->setLongitude($this->getFieldValue($sheet->getCell('W' . $i)->getValue()));
                    $em->persist($senator);
                }
            }
            elseif($categoryId === 8) {
                $referer = $this->generateUrl('deputy_index');
                $sheetName = '08- Députés';
                $inputFileType = IOFactory::identify($spreadsheetFile);
                $reader = IOFactory::createReader($inputFileType);
                $reader->setLoadSheetsOnly($sheetName);
                $spreadsheet = $reader->load($spreadsheetFile);
                $sheet = $spreadsheet->getActiveSheet();
                $sheetRows = $sheet->getHighestRow();
                for($i = 2; $i <= $sheetRows; $i++) {
                    $deputy = new Deputy();
                    $deputy
                        ->setDepartmentCode($this->getFieldValue($sheet->getCell('A' . $i)->getValue(), null, false, 'department'))
                        ->setDepartmentLabel($this->getFieldValue($sheet->getCell('B' . $i)->getValue()))
                        ->setDepartmentCapital($this->getFieldValue($sheet->getCell('C' . $i)->getValue()))
                        ->setDepartmentPopulation($this->getFieldValue($sheet->getCell('D' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setDepartmentSurface($this->getFieldValue($sheet->getCell('E' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setDepartmentDensity($this->getFieldValue($sheet->getCell('F' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setAreaLabel($this->getFieldValue($sheet->getCell('G' . $i)->getValue()))
                        ->setNbDepartments($this->getFieldValue($sheet->getCell('H' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaCapital($this->getFieldValue($sheet->getCell('I' . $i)->getValue()))
                        ->setAreaSurface($this->getFieldValue($sheet->getCell('J' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaPopulation($this->getFieldValue($sheet->getCell('K' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaDensity($this->getFieldValue($sheet->getCell('L' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setLegislativeCirCode($this->getFieldValue($sheet->getCell('M' . $i)->getValue()))
                        ->setLegislativeCirLabel($this->getFieldValue($sheet->getCell('N' . $i)->getValue()))
                        ->setLastName($this->getFieldValue($sheet->getCell('O' . $i)->getValue()))
                        ->setFirstName($this->getFieldValue($sheet->getCell('P' . $i)->getValue()))
                        ->setGender($this->getFieldValue($sheet->getCell('Q' . $i)->getValue()))
                        ->setBirthDate($this->getFieldValue($sheet->getCell('R' . $i)->getFormattedValue(), null, true))
                        ->setProfessionCode($this->getFieldValue($sheet->getCell('S' . $i)->getValue()))
                        ->setProfessionLabel($this->getFieldValue($sheet->getCell('T' . $i)->getValue()))
                        ->setMandateStartDate($this->getFieldValue($sheet->getCell('U' . $i)->getFormattedValue(), null, true))
                        ->setPopulationYear($this->getFieldValue($sheet->getCell('V' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setNbPublicService($this->getFieldValue($sheet->getCell('W' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT));
                        $deputy->setLatitude($this->getFieldValue($sheet->getCell('X' . $i)->getValue()));
                        $deputy->setLongitude($this->getFieldValue($sheet->getCell('Y' . $i)->getValue()));

                    $em->persist($deputy);
                }

            }
            elseif($categoryId === 9) {
                $referer = $this->generateUrl('mayor_index');
                $sheetName = '09- Maires';
                $inputFileType = IOFactory::identify($spreadsheetFile);
                $reader = IOFactory::createReader($inputFileType);
                $reader->setLoadSheetsOnly($sheetName);
                $spreadsheet = $reader->load($spreadsheetFile);
                $sheet = $spreadsheet->getActiveSheet();
                $sheetRows = $sheet->getHighestRow();
                for($i = 2; $i <= $sheetRows; $i++) {
                    $mayor = new Mayor();
                    $mayor
                        ->setDepartmentCode($this->getFieldValue($sheet->getCell('A' . $i)->getValue(), null, false, 'department'))
                        ->setDepartmentLabel($this->getFieldValue($sheet->getCell('B' . $i)->getValue()))
                        ->setCodeInsee($this->getFieldValue($sheet->getCell('C' . $i)->getValue(), null, false, 'commune'))
                        ->setDepartmentCapital($this->getFieldValue($sheet->getCell('D' . $i)->getValue()))
                        ->setDepartmentPopulation($this->getFieldValue($sheet->getCell('E' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setDepartmentSurface($this->getFieldValue($sheet->getCell('F' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setDepartmentDensity($this->getFieldValue($sheet->getCell('G' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setAreaLabel($this->getFieldValue($sheet->getCell('H' . $i)->getValue()))
                        ->setNbDepartments($this->getFieldValue($sheet->getCell('I' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaCapital($this->getFieldValue($sheet->getCell('J' . $i)->getValue()))
                        ->setAreaSurface($this->getFieldValue($sheet->getCell('K' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaPopulation($this->getFieldValue($sheet->getCell('L' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setAreaDensity($this->getFieldValue($sheet->getCell('M' . $i)->getValue(), FILTER_SANITIZE_NUMBER_FLOAT))
                        ->setCommuneLabel($this->getFieldValue($sheet->getCell('N' . $i)->getValue()))
                        ->setLastName($this->getFieldValue($sheet->getCell('O' . $i)->getValue()))
                        ->setFirstName($this->getFieldValue($sheet->getCell('P' . $i)->getValue()))
                        ->setGender($this->getFieldValue($sheet->getCell('Q' . $i)->getValue()))
                        ->setBirthDate($this->getFieldValue($sheet->getCell('R' . $i)->getFormattedValue(), null, true))
                        ->setProfessionCode($this->getFieldValue($sheet->getCell('S' . $i)->getValue()))
                        ->setProfessionLabel($this->getFieldValue($sheet->getCell('T' . $i)->getValue()))
                        ->setMandateStartDate($this->getFieldValue($sheet->getCell('U' . $i)->getFormattedValue(), null, true))
                        ->setFunctionStartDate($this->getFieldValue($sheet->getCell('V' . $i)->getFormattedValue(), null, true))
                        ->setZipCode($this->getFieldValue($sheet->getCell('W' . $i)->getValue(), null, false, 'commune'))
                        ->setPopulationYear($this->getFieldValue($sheet->getCell('X' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT))
                        ->setNbPublicService($this->getFieldValue($sheet->getCell('Y' . $i)->getValue(), FILTER_SANITIZE_NUMBER_INT));
                        $mayor->setLatitude($this->getFieldValue($sheet->getCell('Z' . $i)->getValue()));
                        $mayor->setLongitude($this->getFieldValue($sheet->getCell('AA' . $i)->getValue()));
                    $em->persist($mayor);

                }
            }
            else {
                throw new NotFoundHttpException('La page demandée est introuvable');
            }
            $em->flush();
            $this->addFlash('success', 'Importation effectué avec succès');
            return $this->redirect($referer);
        }
        return $this->render('manager/elected_member/import_spreadsheet.html.twig', [
            'form' => $spreadForm->createView(),
            'categoryId' => $categoryId,
            'referer' => $referer ?? null
        ]);
    }



    private function extractCode($code, $source){

        $type = gettype($code);

        if($type === 'integer') {
            if($source === 'commune') {
                if($code < 10) {
                    return '0000' . $code;
                }
                elseif ($code < 100) {
                    return '000' . $code;
                }
                elseif ($code < 1000) {
                    return '00' . $code;
                }
                elseif ($code < 10000) {
                    return '0' . $code;
                }
                return $code;
            }
            else {
                if( $code < 10) {
                    return '0' . $code;
                }
                return $code;
            }
        }

        return $code;
    }
}
