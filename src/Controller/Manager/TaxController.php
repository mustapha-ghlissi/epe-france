<?php

namespace App\Controller\Manager;

use App\Entity\Tax;
use App\Form\SpreadSheetType;
use App\Form\TaxType;
use App\Repository\TaxRepository;
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
 * @Route("/manager/tax")
 */
class TaxController extends AbstractController
{
    private $templateDir = 'manager/tax/';

    /**
     * @Route("/", name="tax_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        if($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $search = $request->query->get('search')['value'];

            $dql = "SELECT t FROM App\Entity\Tax t";
            if(!empty($search)) {
                $dql = " where t.year like '%" . $search . "%'";
                $dql .= "OR t.codeInsee like '%" . $search . "%'";
                $dql .= "OR t.communeLabel like '%" . $search . "%'";
                $dql .= "OR t.departmentLabel like '%" . $search . "%'";
                $dql .= "OR t.areaLabel like '%" . $search . "%'";
                $dql .= "OR t.totalAmount like '%" . $search . "%'";
            }


            $column = (int)$request->query->get('order')[0]['column'];
            $mode = $request->query->get('order')[0]['dir'];
            if($column === 0) {
                $dql .= " ORDER BY t.id ";
            }
            elseif($column === 1) {
                $dql .= " ORDER BY t.year ";
            }
            elseif($column === 2) {
                $dql .= " ORDER BY t.codeInsee ";
            }
            elseif($column === 3) {
                $dql .= " ORDER BY t.communeLabel ";
            }
            elseif($column === 4) {
                $dql .= " ORDER BY t.departmentLabel ";
            }
            elseif($column === 5) {
                $dql .= " ORDER BY t.areaLabel ";
            }
            else {
                $dql .= " ORDER BY t.totalAmount ";
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
     * @Route("/new", name="tax_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $tax = new Tax();
        $form = $this->createForm(TaxType::class, $tax);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tax);
            $entityManager->flush();
            $this->addFlash('success', 'Enregistrement effecté avec succès');
            return $this->redirectToRoute('tax_index');
        }

        return $this->render("{$this->templateDir}new.html.twig", [
            'tax' => $tax,
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
     * @Route("/import", name="tax_import", methods={"GET", "POST"})
     */
    public function import(Request $request, TaxRepository $taxRepository) {
        $spreadForm = $this->createForm(SpreadSheetType::class, null, [
            'method' => Request::METHOD_POST
        ]);
        $spreadForm->handleRequest($request);
        if($spreadForm->isSubmitted() && $spreadForm->isValid()) {
            //$taxRepository->resetTable();
            $em = $this->getDoctrine()->getManager();
            /**
             * @var UploadedFile $spreadsheetFile
             */
            $spreadsheetFile = $spreadForm->get('file')->getData();
            $sheetName = 'Impot sur le revenu';
            $inputFileType = IOFactory::identify($spreadsheetFile);
            $reader = IOFactory::createReader($inputFileType);
            $reader->setLoadSheetsOnly($sheetName);
            $spreadsheet = $reader->load($spreadsheetFile);
            $sheet = $spreadsheet->getActiveSheet();
            $sheetRows = $sheet->getHighestRow();

            for($i = 2; $i <= $sheetRows; $i++) {
                $tax = new Tax();
                $tax
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
                    ->setNbTaxHomes($sheet->getCell('P' . $i)->getValue())
                    ->setTaxRevenue($sheet->getCell('Q' . $i)->getValue())
                    ->setTotalAmount($sheet->getCell('R' . $i)->getValue())
                    ->setNbImposableTaxHomes($sheet->getCell('S' . $i)->getValue())
                    ->setImposableTaxRevenue($sheet->getCell('T' . $i)->getValue())
                    ->setSalaryNbTaxHomes($sheet->getCell('U' . $i)->getValue())
                    ->setSalaryTaxRevenue($sheet->getCell('V' . $i)->getValue())
                    ->setPensionNbTaxHomes($sheet->getCell('W' . $i)->getValue())
                    ->setPensionTaxRevenue($sheet->getCell('X' . $i)->getValue())
                    ->setSource($sheet->getCell('Y' . $i)->getValue());

                $em->persist($tax);
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
     * @Route("/{id}", name="tax_show", methods={"GET"})
     */
    public function show(Tax $tax): Response
    {
        return $this->render("{$this->templateDir}show.html.twig", [
            'tax' => $tax,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tax_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tax $tax): Response
    {
        $form = $this->createForm(TaxType::class, $tax);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Mise à jour effectée avec succès');
            return $this->redirectToRoute('tax_index');
        }

        return $this->render("{$this->templateDir}edit.html.twig", [
            'tax' => $tax,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tax_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tax $tax): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tax->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tax);
            $entityManager->flush();
            $this->addFlash('success', 'Suppression effectée avec succès');
        }

        return $this->redirectToRoute('tax_index');
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
