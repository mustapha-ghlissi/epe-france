<?php

namespace App\Controller\Manager;

use App\Entity\Accounting;
use App\Form\AccountingType;
use App\Form\SpreadSheetType;
use App\Repository\AccountingRepository;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Carbon\Carbon;

/**
 * @Route("/manager/accounting")
 */
class AccountingController extends AbstractController
{
    private $templateDir = 'manager/accounting/';

    /**
     * @Route("/", name="accounting_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        if($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $search = $request->query->get('search')['value'];
            if(!empty($search)) {
                $dql = "SELECT a FROM App\Entity\Accounting a where a.year like '%" . $search . "%'";
                $dql .= "OR a.codeInsee like '%" . $search . "%'";
                $dql .= "OR a.communeLabel like '%" . $search . "%'";
                $dql .= "OR a.departmentLabel like '%" . $search . "%'";
                $dql .= "OR a.areaLabel like '%" . $search . "%'";
                $dql .= "OR a.population like '%" . $search . "%'";
            }
            else {
                $dql = "SELECT a FROM App\Entity\Accounting a";
            }

            $column = (int)$request->query->get('order')[0]['column'];
            $mode = $request->query->get('order')[0]['dir'];
            if($column === 0) {
                $dql .= " ORDER BY a.id ";
            }
            elseif($column === 1) {
                $dql .= " ORDER BY a.year ";
            }
            elseif($column === 2) {
                $dql .= " ORDER BY a.codeInsee ";
            }
            elseif($column === 3) {
                $dql .= " ORDER BY a.communeLabel ";
            }
            elseif($column === 4) {
                $dql .= " ORDER BY a.departmentLabel ";
            }
            elseif($column === 5) {
                $dql .= " ORDER BY a.areaLabel ";
            }
            else {
                $dql .= " ORDER BY a.population ";
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

            return $this->json([
                'draw' => (int)$request->query->getInt('draw'),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data
            ]);
        }

        return $this->render("{$this->templateDir}index.html.twig");
    }

    /**
     * @Route("/new", name="accounting_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $accounting = new Accounting();
        $form = $this->createForm(AccountingType::class, $accounting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($accounting);
            $entityManager->flush();
            $this->addFlash('success', 'Enregistrement effecté avec succès');
            return $this->redirectToRoute('accounting_index');
        }

        return $this->render("{$this->templateDir}new.html.twig", [
            'accounting' => $accounting,
            'form' => $form->createView(),
        ]);
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
     * @param Request $request
     * @return Response
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @Route("/import", name="accounting_import", methods={"GET", "POST"})
     */
    public function import(Request $request, AccountingRepository $accountingRepository) {
        $spreadForm = $this->createForm(SpreadSheetType::class, null, [
            'method' => Request::METHOD_POST
        ]);
        $spreadForm->handleRequest($request);
        if($spreadForm->isSubmitted() && $spreadForm->isValid()) {
            //$accountingRepository->resetTable();
            $em = $this->getDoctrine()->getManager();
            /**
             * @var UploadedFile $spreadsheetFile
             */
            $spreadsheetFile = $spreadForm->get('file')->getData();
            $sheetName = 'Compte comptable et financier';
            $inputFileType = IOFactory::identify($spreadsheetFile);
            $reader = IOFactory::createReader($inputFileType);
            $reader->setLoadSheetsOnly($sheetName);
            $spreadsheet = $reader->load($spreadsheetFile);
            $sheet = $spreadsheet->getActiveSheet();
            $sheetRows = $sheet->getHighestRow();


            for($i = 2; $i <= $sheetRows; $i++) {
                $accounting = new Accounting();
                $accounting
                    ->setYear($sheet->getCell('A' . $i)->getValue())
                    ->setCodeInsee($this->getFieldValue($sheet->getCell('B' . $i)->getValue(), null, false, 'commune'))
                    ->setCommuneLabel($sheet->getCell('C' . $i)->getValue())
                    ->setCommuneLastName($sheet->getCell('D' . $i)->getValue())
                    ->setCommuneFirstName($sheet->getCell('E' . $i)->getValue())
                    ->setCommuneBirthDate($this->getFieldValue($sheet->getCell('F' . $i)->getValue(), null, true))
                    ->setDepartmentCode($this->getFieldValue($sheet->getCell('G' . $i)->getValue(), null, false, 'department'))
                    ->setDepartmentLabel($sheet->getCell('H' . $i)->getValue())
                    ->setDepLastName($sheet->getCell('I' . $i)->getValue())
                    ->setDepFirstName($sheet->getCell('J' . $i)->getValue())
                    ->setDepBirthDate($this->getFieldValue($sheet->getCell('K' . $i)->getValue(), null, true))
                    ->setAreaLabel($sheet->getCell('L' . $i)->getValue())
                    ->setAreaLastName($sheet->getCell('M' . $i)->getValue())
                    ->setAreaFirstName($sheet->getCell('N' . $i)->getValue())
                    ->setAreaBirthDate($this->getFieldValue($sheet->getCell('O' . $i)->getValue(), null, true))
                    ->setPopulation($sheet->getCell('P' . $i)->getValue())
                    ->setGroupingType($sheet->getCell('Q' . $i)->getValue())
                    ->setProductsTotal($sheet->getCell('R' . $i)->getValue())
                    ->setLocalTax($sheet->getCell('S' . $i)->getValue())
                    ->setOtherTax($sheet->getCell('T' . $i)->getValue())
                    ->setGlobalAllocation($sheet->getCell('U' . $i)->getValue())
                    ->setTotalExpenses($sheet->getCell('V' . $i)->getValue())
                    ->setPersonalExpenses($sheet->getCell('W' . $i)->getValue())
                    ->setExternalExpenses($sheet->getCell('X' . $i)->getValue())
                    ->setFinancialExpenses($sheet->getCell('Y' . $i)->getValue())
                    ->setGrants($sheet->getCell('Z' . $i)->getValue())
                    ->setHousingTax($sheet->getCell('AA' . $i)->getValue())
                    ->setPropertyTax($sheet->getCell('AB' . $i)->getValue())
                    ->setNoPropertyTax($sheet->getCell('AC' . $i)->getValue())
                    ->setBrankCredits($sheet->getCell('AD' . $i)->getValue())
                    ->setReceivedGrants($sheet->getCell('AE' . $i)->getValue())
                    ->setEquipmentExpenses($sheet->getCell('AF' . $i)->getValue())
                    ->setCreditRefund($sheet->getCell('AG' . $i)->getValue())
                    ->setDebtAnnuity($sheet->getCell('AH' . $i)->getValue())
                    ->setSource($sheet->getCell('AI' . $i)->getValue());

                $em->persist($accounting);
            }

            $em->flush();
            $this->addFlash('success', 'Importation effectuée avec succès.');
        }
        return $this->render("{$this->templateDir}import.html.twig",
            [
                'form' => $spreadForm->createView()
            ]);
    }

    /**
     * @Route("/{id}", name="accounting_show", methods={"GET"})
     */
    public function show(Accounting $accounting): Response
    {
        return $this->render("{$this->templateDir}show.html.twig", [
            'accounting' => $accounting,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="accounting_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Accounting $accounting): Response
    {
        $form = $this->createForm(AccountingType::class, $accounting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Mise à jour effectée avec succès');
            return $this->redirectToRoute('accounting_index');
        }

        return $this->render("{$this->templateDir}edit.html.twig", [
            'accounting' => $accounting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="accounting_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Accounting $accounting): Response
    {
        if ($this->isCsrfTokenValid('delete'.$accounting->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($accounting);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectée avec succès');
        }

        return $this->redirectToRoute('accounting_index');
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
