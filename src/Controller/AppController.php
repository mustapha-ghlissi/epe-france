<?php

namespace App\Controller;

use App\Controller\Manager\ElectedMemberController;
use App\Entity\CommunityAdvisor;
use App\Entity\CorsicanAdvisor;
use App\Entity\DepartmentalAdvisor;
use App\Entity\Deputy;
use App\Entity\Tax;
use App\Entity\Accounting;
use App\Entity\DeputyNote;
use App\Entity\EuroDeputy;
use App\Entity\EuroDeputyNote;
use App\Entity\ExtraData;
use App\Entity\Mayor;
use App\Entity\MPDPRNote;
use App\Entity\MunicipalAdvisor;
use App\Entity\OtherNote;
use App\Entity\RegionalAdvisor;
use App\Entity\Senator;
use App\Form\DeputyNoteType;
use App\Form\EuroDeputyNoteType;
use App\Form\MPDPRNoteType;
use App\Form\OtherNoteType;
use App\Form\SearchType;
use App\Repository\AccountingRepository;
use App\Repository\BoardMinuteRepository;
use App\Repository\CommuneStatRepository;
use App\Repository\CommunityAdvisorRepository;
use App\Repository\CorsicanAdvisorRepository;
use App\Repository\DepartmentalAdvisorRepository;
use App\Repository\DeputyNoteRepository;
use App\Repository\DeputyRepository;
use App\Repository\EuroDeputyNoteRepository;
use App\Repository\EuroDeputyRepository;
use App\Repository\ExtraDataRepository;
use App\Repository\MayorRepository;
use App\Repository\MPDPRNoteRepository;
use App\Repository\MunicipalAdvisorRepository;
use App\Repository\OtherNoteRepository;
use App\Repository\RegionalAdvisorRepository;
use App\Repository\SenatorRepository;
use App\Repository\TaxRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AppController extends AbstractController
{
    const CATEGORIES = [
        'liste-des-conseillers-municipaux',
        'liste-des-conseillers-communautaires',
        'liste-des-conseillers-departementaux',
        'liste-des-conseillers-regionaux',
        'liste-des-conseillers-corse',
        'liste-des-deputes-europeens',
        'liste-des-senateurs',
        'liste-des-deputes',
        'liste-des-maires',
        'liste-des-presidents-des-departements',
        'liste-des-presidents-des-regions'
    ];

    /**
     * @param Request $request
     * @param SessionInterface $session
     * @return RedirectResponse|Response
     * @Route("/", name="app", methods={"GET", "POST"})
     */
    public function index(Request $request, SessionInterface $session)
    {
        if ($request->isMethod(Request::METHOD_POST) && $request->request->has('categoryId')) {
            $categoryId = (int)$request->request->get('categoryId');
            $session->set('categoryId', $categoryId);
            $categoryName = $this->getListName($categoryId);
            return $this->redirectToRoute('elected_members_list', ['categoryName' => $categoryName]);
        }


        return $this->render('app/index.html.twig');
    }

    /**
     * @param Request $request
     * @param MPDPRNoteRepository $MPDPRNoteRepository
     * @param OtherNoteRepository $otherNoteRepository
     * @param DeputyNoteRepository $deputyNoteRepository
     * @param EuroDeputyNoteRepository $euroDeputyNoteRepository
     * @param MunicipalAdvisorRepository $municipalAdvisorRepository
     * @param CommunityAdvisorRepository $communityAdvisorRepository
     * @param DepartmentalAdvisorRepository $departmentalAdvisorRepository
     * @param RegionalAdvisorRepository $regionalAdvisorRepository
     * @param CorsicanAdvisorRepository $corsicanAdvisorRepository
     * @param EuroDeputyRepository $euroDeputyRepository
     * @param SenatorRepository $senatorRepository
     * @param DeputyRepository $deputyRepository
     * @param MayorRepository $mayorRepository
     * @param SerializerInterface $serializer
     * @return Response
     * @Route("/app-elected-members", name="app_elected_members", methods={"GET"})
     */
    public function appElectedMembers(Request $request,
                                      MPDPRNoteRepository $MPDPRNoteRepository,
                                      OtherNoteRepository $otherNoteRepository,
                                      DeputyNoteRepository $deputyNoteRepository,
                                      EuroDeputyNoteRepository $euroDeputyNoteRepository,
                                      MunicipalAdvisorRepository $municipalAdvisorRepository,
                                      CommunityAdvisorRepository $communityAdvisorRepository,
                                      DepartmentalAdvisorRepository $departmentalAdvisorRepository,
                                      RegionalAdvisorRepository $regionalAdvisorRepository,
                                      CorsicanAdvisorRepository $corsicanAdvisorRepository,
                                      EuroDeputyRepository $euroDeputyRepository,
                                      SenatorRepository $senatorRepository,
                                      DeputyRepository $deputyRepository,
                                      MayorRepository $mayorRepository, SerializerInterface $serializer)
    {
        if($request->isXmlHttpRequest()) {
            $municipalAdvisors = $otherNoteRepository->getMunicipalAdvisors();
            $communityAdvisors = $otherNoteRepository->getCommunityAdvisors();
            $departmentalAdvisors = $otherNoteRepository->getDepartmentalAdvisors();
            $regionalAdvisors = $otherNoteRepository->getRegionalAdvisors();
            $corsicanAdvisors = $otherNoteRepository->getCorsicanAdvisors();
            $senators = $otherNoteRepository->getSenators();
            $deputies = $deputyNoteRepository->getDeputies();
            $euroDeputies = $euroDeputyNoteRepository->getDeputies();
            $departmentalPresidents = $MPDPRNoteRepository->getDepartmentalPresidents();
            $regionalPresidents = $MPDPRNoteRepository->getRegionalPresidents();
            $mayors = $MPDPRNoteRepository->getMayors();
            if (count($municipalAdvisors) < 10) {
                $count = count($municipalAdvisors);
                $municipalAdvisors = array_merge($municipalAdvisors, $municipalAdvisorRepository->getNotNoted(10 - $count));
            }
            if (count($communityAdvisors) < 10) {
                $count = count($communityAdvisors);
                $communityAdvisors = array_merge($communityAdvisors, $communityAdvisorRepository->getNotNoted(10 - $count));
            }
            if (count($departmentalAdvisors) < 10) {
                $count = count($departmentalAdvisors);
                $departmentalAdvisors = array_merge($departmentalAdvisors, $departmentalAdvisorRepository->getNotNoted(10 - $count));
            }
            if (count($regionalAdvisors) < 10) {
                $count = count($regionalAdvisors);
                $regionalAdvisors = array_merge($regionalAdvisors, $regionalAdvisorRepository->getNotNoted(10 - $count));
            }
            if (count($corsicanAdvisors) < 10) {
                $count = count($corsicanAdvisors);
                $corsicanAdvisors = array_merge($corsicanAdvisors, $corsicanAdvisorRepository->getNotNoted(10 - $count));
            }
            if (count($senators) < 10) {
                $count = count($senators);
                $senators = array_merge($senators, $senatorRepository->getNotNoted( 10 - $count));
            }
            if (count($deputies) < 10) {
                $count = count($deputies);
                $deputies = array_merge($deputies, $deputyRepository->getNotNoted(10 - $count));
            }
            if (count($euroDeputies) < 10) {
                $count = count($euroDeputies);
                $euroDeputies = array_merge($euroDeputies, $euroDeputyRepository->getNotNoted(10 - $count));
            }
            if (count($mayors) < 10) {
                $count = count($mayors);
                $mayors = array_merge($mayors, $mayorRepository->getNotNoted(10 - $count));
            }
            if (count($departmentalPresidents) < 10) {
                $count = count($departmentalPresidents);
                $departmentalPresidents = array_merge($departmentalPresidents, $departmentalAdvisorRepository->getPresidents(10 - $count));
            }
            if (count($regionalPresidents) < 10) {
                $count = count($regionalPresidents);
                $regionalPresidents = array_merge($regionalPresidents, $regionalAdvisorRepository->getPresidents(10 - $count));
            }
            $data = [
                'municipalAdvisors' => $municipalAdvisors,
                'communityAdvisors' => $communityAdvisors,
                'departmentalAdvisors' => $departmentalAdvisors,
                'regionalAdvisors' => $regionalAdvisors,
                'corsicanAdvisors' => $corsicanAdvisors,
                'euroDeputies' => $euroDeputies,
                'mayors' => $mayors,
                'deputies' => $deputies,
                'senators' => $senators,
                'departmentalPresidents' => $departmentalPresidents,
                'regionalPresidents' => $regionalPresidents
            ];
            $data = $serializer->serialize($data, 'json', ['groups' => '']);
            return new Response($data, Response::HTTP_OK, ['Content-Type' => 'application/json']);
        }
        throw new NotFoundHttpException('Page introuvable !');
    }

    /**
     * @param Request $request
     * @param MailerInterface $mailer
     * @return JsonResponse|Response
     * @throws TransportExceptionInterface
     * @Route("/contactez-nous", name="app_contact", methods={"GET", "POST"})
     */
    public function contact(Request $request, MailerInterface $mailer, ParameterBagInterface $parameterBag)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod(Request::METHOD_POST)) {
            $data = json_decode($request->getContent(), true);
            $contactEmail = $parameterBag->get('mailer.email');
            $email = (new Email())
                ->from($contactEmail)
                ->to($contactEmail) // Set email here
                ->replyTo($data['email'])
                ->subject("Nouveau message de {$data['fullName']}: {$data['subject']}")
                ->text($data['message'])
                ->html("<p>{$data['message']}</p>");
            $mailer->send($email);

            return $this->json([
                'message' => 'Votre message à été envoyé avec succès',
                'status' => true
            ], Response::HTTP_OK);
        }

        return $this->render('app/contact.html.twig');
    }

    /**
     * @return Response
     * @Route("/sitemap.xml", name="app_sitemap", defaults={"_format"="xml"})
     */
    public function sitemap()
    {
        return $this->render('app/sitemap.xml');
    }

    /**
     * @return Response
     * @Route("/a-propos", name="app_about")
     */
    public function about()
    {
        return $this->render('app/about.html.twig');
    }

    /**
     * @return Response
     * @Route("/conditions-générales", name="app_cgu")
     */
    public function cgu()
    {
        return $this->render('app/cgu.html.twig');
    }

    /**
     * @return Response
     * @Route("/mentions-légales", name="app_legal")
     */
    public function legal()
    {
        return $this->render('app/legal.html.twig');
    }

    /**
     * @return Response
     * @Route("/rgpd", name="app_rgpd")
     */
    public function rgpd()
    {
        return $this->render('app/rgpd.html.twig');
    }

    /**
     * @return Response
     * @Route("/plan-du-site", name="app_plan")
     */
    public function plan()
    {
        return $this->render('app/plan.html.twig');
    }

    /**
     * @param Request $request
     * @param DepartmentalAdvisorRepository $departmentalAdvisorRepository
     * @param RegionalAdvisorRepository $regionalAdvisorRepository
     * @param MayorRepository $mayorRepository
     * @param MunicipalAdvisorRepository $municipalAdvisorRepository
     * @param CommunityAdvisorRepository $communityAdvisorRepository
     * @param DeputyRepository $deputyRepository
     * @param EuroDeputyRepository $euroDeputyRepository
     * @param CorsicanAdvisorRepository $corsicanAdvisorRepository
     * @param SenatorRepository $senatorRepository
     * @param PaginatorInterface $paginator
     * @param SerializerInterface $serializer
     * @param SessionInterface $session
     * @return Response
     * @Route("/{categoryName}", name="elected_members_list", methods={"GET", "POST"})
     */
    public function electedMembersList(Request $request,
                                       string $categoryName,
                                       DepartmentalAdvisorRepository $departmentalAdvisorRepository,
                                       RegionalAdvisorRepository $regionalAdvisorRepository,
                                       MayorRepository $mayorRepository,
                                       MunicipalAdvisorRepository $municipalAdvisorRepository,
                                       CommunityAdvisorRepository $communityAdvisorRepository,
                                       DeputyRepository $deputyRepository,
                                       EuroDeputyRepository $euroDeputyRepository,
                                       CorsicanAdvisorRepository $corsicanAdvisorRepository,
                                       SenatorRepository $senatorRepository,
                                       PaginatorInterface $paginator, SerializerInterface $serializer,
                                       SessionInterface $session
    )
    {

        if ($request->isMethod(Request::METHOD_POST) && $request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $categoryId = (int)$data['categoryId'];
            $criteria = $data['criteria'];

            if ($categoryId === 1) {
                $members = $municipalAdvisorRepository->getByCriteria($criteria);
                $members = $serializer->serialize($members, 'json', ['groups' => 'search:read']);
                return new Response($members, Response::HTTP_OK, ['Content-Type' => 'application/json']);
            } elseif ($categoryId === 2) {
                $members = $communityAdvisorRepository->getByCriteria($criteria);
                $members = $serializer->serialize($members, 'json', ['groups' => 'search:read']);
                return new Response($members, Response::HTTP_OK, ['Content-Type' => 'application/json']);
            } elseif ($categoryId === 3) {
                $members = $departmentalAdvisorRepository->getByCriteria($criteria);
                $members = $serializer->serialize($members, 'json', ['groups' => 'search:read']);
                return new Response($members, Response::HTTP_OK, ['Content-Type' => 'application/json']);
            } elseif ($categoryId === 4) {
                $members = $regionalAdvisorRepository->getByCriteria($criteria);
                $members = $serializer->serialize($members, 'json', ['groups' => 'search:read']);
                return new Response($members, Response::HTTP_OK, ['Content-Type' => 'application/json']);
            } elseif ($categoryId === 5) {
                $members = $corsicanAdvisorRepository->getByCriteria($criteria);
                $members = $serializer->serialize($members, 'json', ['groups' => 'search:read']);
                return new Response($members, Response::HTTP_OK, ['Content-Type' => 'application/json']);
            } elseif ($categoryId === 6) {
                $members = $euroDeputyRepository->getByCriteria($criteria);
                $members = $serializer->serialize($members, 'json', ['groups' => 'search:read']);
                return new Response($members, Response::HTTP_OK, ['Content-Type' => 'application/json']);
            } elseif ($categoryId === 7) {
                $members = $senatorRepository->getByCriteria($criteria);
                $members = $serializer->serialize($members, 'json', ['groups' => 'search:read']);
                return new Response($members, Response::HTTP_OK, ['Content-Type' => 'application/json']);
            } elseif ($categoryId === 8) {
                $members = $deputyRepository->getByCriteria($criteria);
                $members = $serializer->serialize($members, 'json', ['groups' => 'search:read']);
                return new Response($members, Response::HTTP_OK, ['Content-Type' => 'application/json']);
            } elseif ($categoryId === 9) {
                $members = $mayorRepository->getByCriteria($criteria);
                $members = $serializer->serialize($members, 'json', ['groups' => 'search:read']);
                return new Response($members, Response::HTTP_OK, ['Content-Type' => 'application/json']);
            } elseif ($categoryId === 10) {
                $members = $departmentalAdvisorRepository->getPresidentsByCriteria($criteria);
                $members = $serializer->serialize($members, 'json', ['groups' => 'search:read']);
                return new Response($members, Response::HTTP_OK, ['Content-Type' => 'application/json']);
            } else {
                $members = $regionalAdvisorRepository->getPresidentsByCriteria($criteria);
                $members = $serializer->serialize($members, 'json', ['groups' => 'search:read']);
                return new Response($members, Response::HTTP_OK, ['Content-Type' => 'application/json']);
            }
        }

        $path = $request->getPathInfo();
        if(substr($path, 0, strlen("/manager")) === "/manager") {
            return $this->redirectToRoute('manager');
        }


        if(!in_array($categoryName, self::CATEGORIES)) {
            throw new NotFoundHttpException('Page introuvable !');
        }

        if ($request->isMethod(Request::METHOD_POST) && $request->request->has('categoryId')) {
            $categoryId = (int)$request->request->get('categoryId');
            $session->set('categoryId', $categoryId);
        } elseif($session->has('categoryId')) {
            $categoryId = (int)$session->get('categoryId');
        }
        else {
            $categoryId = $this->getCategoryByName($categoryName);
            $session->set('categoryId', $categoryId);
        }

        $data = $this->getElectedMembers($categoryId);

        if (!$data) {
            return $this->redirectToRoute('app');
        }

        $electedMembers = $paginator->paginate(
            $data['query'], /* query NOT result */
            $request->query->getInt('page', 1),
            7
        );

        $noteLinks = $this->getNoteLinks();

        return $this->render('app/elected_member/list.html.twig', [
            'electedMembers' => $electedMembers,
            'title' => $data['title'],
            'categoryId' => $categoryId,
            'noteLinks' => $noteLinks,
        ]);
    }

    private function getCategoryByName(string $name): int {



        $categoryId = null;
        switch($name) {
            case 'liste-des-conseillers-municipaux':
                $categoryId = 1;
                break;
            case 'liste-des-conseillers-communautaires':
                $categoryId = 2;
                break;
            case 'liste-des-conseillers-departementaux':
                $categoryId = 3;
                break;
            case 'liste-des-conseillers-regionaux':
                $categoryId = 4;
                break;
            case 'liste-des-conseillers-corse':
                $categoryId = 5;
                break;
            case 'liste-des-deputes-europeens':
                $categoryId = 6;
                break;
            case 'liste-des-senateurs':
                $categoryId = 7;
                break;
            case 'liste-des-deputes':
                $categoryId = 8;
                break;
            case 'liste-des-maires':
                $categoryId = 9;
                break;
            case 'liste-des-presidents-des-departements':
                $categoryId = 10;
                break;
            default:
                $categoryId = 11;
                break;
        }

        return $categoryId;
    }

    /**
     * @param Request $request
     * @param SessionInterface $session
     * @param CommuneStatRepository $communeStatRepository
     * @param AccountingRepository $accountingRepository
     * @param TaxRepository $taxRepository
     * @param MunicipalAdvisorRepository $municipalAdvisorRepository
     * @param CommunityAdvisorRepository $communityAdvisorRepository
     * @param DepartmentalAdvisorRepository $departmentalAdvisorRepository
     * @param RegionalAdvisorRepository $regionalAdvisorRepository
     * @param CorsicanAdvisorRepository $corsicanAdvisorRepository
     * @param EuroDeputyRepository $euroDeputyRepository
     * @param DeputyRepository $deputyRepository
     * @param MayorRepository $mayorRepository
     * @param SenatorRepository $senatorRepository
     * @return RedirectResponse|Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @Route("/{categoryName}/evaluation", name="elected_member_details", methods={"GET", "POST"})
     */
    public function getElected(Request $request,
                               SessionInterface $session,
                               BoardMinuteRepository $boardMinuteRepository,
                               ExtraDataRepository $extraDataRepository,
                               CommuneStatRepository $communeStatRepository,
                               AccountingRepository $accountingRepository,
                               TaxRepository $taxRepository,
                               MunicipalAdvisorRepository $municipalAdvisorRepository,
                               CommunityAdvisorRepository $communityAdvisorRepository,
                               DepartmentalAdvisorRepository $departmentalAdvisorRepository,
                               RegionalAdvisorRepository $regionalAdvisorRepository,
                               CorsicanAdvisorRepository $corsicanAdvisorRepository,
                               EuroDeputyRepository $euroDeputyRepository,
                               DeputyRepository $deputyRepository,
                               MayorRepository $mayorRepository,
                               SenatorRepository $senatorRepository)
    {


        if ($request->isMethod('POST') && $request->request->has('categoryId')) {
            $categoryId = (int)$request->request->get('categoryId');
            $id = (int)$request->request->get('memberId');
            $session->set('categoryId', $categoryId);
            $session->set('memberId', $id);
        } elseif ($session->has('categoryId')) {
            $categoryId = (int)$session->get('categoryId');
            $id = (int)$session->get('memberId');
        } else {
            return $this->redirectToRoute('app');
        }


        $electedMember = $this->getElectedMember($categoryId, $id, $municipalAdvisorRepository,
            $communityAdvisorRepository,
            $departmentalAdvisorRepository,
            $regionalAdvisorRepository,
            $corsicanAdvisorRepository,
            $euroDeputyRepository,
            $deputyRepository,
            $mayorRepository,
            $senatorRepository);

        if (!is_object($electedMember)) {
            throw new NotFoundHttpException('Page introuvable !');
        }

        $extraData = $this->getExtraDataByElectedMember($extraDataRepository, $electedMember);

        $type = $this->getFormTypeByClassName($electedMember, $categoryId);
        $data = $type['data'];
        $form = $this->createForm($type['type'], $data);
        $form->handleRequest($request);
        $categoryName = $this->getListName($categoryId);
        if ($form->isSubmitted()) {
            /**
             * @var OtherNote $data
             */
            $ipAddress = $request->getClientIp();
            $data->setIpAddress($ipAddress);
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();

            $message = 'Votre évaluation a bien été prise en compte avec succès.';
            $message .= ' Nous vous remercions pour votre participation à l’amélioration et à l’enrichissement de la démocratie participative.';
            $this->redirectToRoute('elected_member_details', ['categoryName' => $categoryName]);
        }

        $noteLinks = $this->getNoteLinks();

        if ($categoryId !== 1 && $categoryId !== 2 && $categoryId !== 6 && $categoryId !== 9) {
            if ($categoryId === 4 || $categoryId === 11) {
                $nbCommunes = $communeStatRepository->getByArea($electedMember->getAreaLabel());
            } else {
                $communeStat = $communeStatRepository->getByDepartment($electedMember->getDepartmentLabel());
                if($communeStat) {
                    $nbCommunes = $communeStat['nbCommunes'];
                }
            }
        }

        $taxAccounting = $this->getTaxesByCategory($electedMember, $taxRepository, $accountingRepository);

        $tax = $taxRepository->getSource();
        if($tax) {
            $source = $tax['source'];
        }
        else {
            $accounting = $accountingRepository->getSource();
            if($accounting) {
                $source = $accounting['source'];
            }
        }

        if($categoryId !== 6) {
            if($categoryId === 1 || $categoryId === 2 || $categoryId === 9) {
                if($categoryId === 2) {
                    $code = $electedMember->getCommuneCode();
                }
                else {
                    $code = $electedMember->getCodeInsee();
                }
                $boardMinutes = $boardMinuteRepository->getByCommune($code, $electedMember->getCommuneLabel());
            }
            elseif ($categoryId === 4 || $categoryId === 11) {
                $boardMinutes = $boardMinuteRepository->getByArea($electedMember->getAreaLabel());
            }
            else {
                $boardMinutes = $boardMinuteRepository->getByDepartment($electedMember->getDepartmentLabel());
            }
        }

        return $this->render('app/elected_member/details.html.twig', [
            'electedMember' => $electedMember,
            'extraData' => $extraData,
            'categoryName' => $categoryName,
            'form' => $form->createView(),
            'name' => $type['name'],
            'noteLinks' => $noteLinks,
            'categoryId' => $categoryId,
            'message' => $message ?? null,
            'nbCommunes' => $nbCommunes ?? null,
            'taxAccountingData' => $taxAccounting,
            'source' => $source ?? null,
            'boardMinutes' => $boardMinutes ?? null
        ]);
    }

    /**
     * @param $electedMember
     * @param TaxRepository $taxRepository
     * @param AccountingRepository $accountingRepository
     * @return |null
     */
    private function getTaxesByCategory($electedMember, TaxRepository $taxRepository, AccountingRepository $accountingRepository) {

        $class = get_class($electedMember);

        if($class === 'App\Entity\EuroDeputy') {
            return null;
        }

        if($class === 'App\Entity\Mayor' || $class === 'App\Entity\MunicipalAdvisor' || $class === 'App\Entity\CommunityAdvisor') {

            if($class === 'App\Entity\CommunityAdvisor') {
              $codeInsee = $electedMember->getCommuneCode();
            }
            else {
              $codeInsee = $electedMember->getCodeInsee();
            }
            $communeTaxes = $taxRepository->getCommuneTaxByCommune($codeInsee);
            $departmentTaxes = $taxRepository->getCommuneTaxByDepartment($electedMember->getDepartmentLabel());
            $areaTaxes = $taxRepository->getCommuneTaxByArea($electedMember->getAreaLabel());
            $communeFinancials = $accountingRepository->getCommuneAccountingByCommune($codeInsee);
            $departmentFinancials = $accountingRepository->getCommuneAccountingByDepartment($electedMember->getDepartmentLabel());
            $areaFinancials = $accountingRepository->getCommuneAccountingByArea($electedMember->getAreaLabel());


            $taxAccountingData = [];
            // Department taxes total /an
            $zipCodes = [];
            foreach ($departmentTaxes as $depKey => $departmentTax) {
                $key = $departmentTax['codeInsee'] . '-' . $departmentTax['communeLabel'];
                if(!array_key_exists($key, $zipCodes)) {
                    $zipCodes[$key] = [];
                }
                array_push($zipCodes[$key], $departmentTax);
            }
            $totals = [];
            foreach ($zipCodes as $zipKey => $zipCode) {
                $aggregatedItem = [];
                $communeLabel = $zipCode[0]['communeLabel'];
                $communeZipCode = $zipCode[0]['codeInsee'];
                $lastYear = $zipCode[0]['year'];
                $firstYear = $zipCode[count($zipCode) - 1]['year'];

                foreach ($zipCode as $key => $data) {
                    foreach ($data as $dataKey => $datum) {
                        if($dataKey !== 'year' && $dataKey !== 'communeLabel' && $dataKey !== 'codeInsee') {
                            if(array_key_exists($dataKey, $aggregatedItem)) {
                                $aggregatedItem[$dataKey] += $datum;
                            }
                            else {
                                $aggregatedItem[$dataKey] = $datum;
                            }
                        }
                    }
                }
                $totals[$zipKey] = $aggregatedItem;
                $totals[$zipKey]['title'] = $firstYear . '-' . $lastYear . ' Total : ' . $communeLabel;
                $totals[$zipKey]['communeLabel'] = $communeLabel;
                $totals[$zipKey]['codeInsee'] = $communeZipCode;
            }
            $departmentTaxes = [];
            $departmentTaxes['data'] = $zipCodes;
            $departmentTaxes['totals'] = $totals;


            // Department financials total /an
            $zipCodes = [];
            foreach ($departmentFinancials as $depKey => $departmentFinancial) {
                $key = $departmentFinancial['codeInsee'] . '-' . $departmentFinancial['communeLabel'];
                if(!array_key_exists($key, $zipCodes)) {
                    $zipCodes[$key] = [];
                }
                array_push($zipCodes[$key], $departmentFinancial);
            }
            $totals = [];
            foreach ($zipCodes as $zipKey => $zipCode) {
                $aggregatedItem = [];
                $communeLabel = $zipCode[0]['communeLabel'];
                $communeZipCode = $zipCode[0]['codeInsee'];
                $lastYear = $zipCode[0]['year'];
                $firstYear = $zipCode[count($zipCode) - 1]['year'];

                foreach ($zipCode as $key => $data) {
                    foreach ($data as $dataKey => $datum) {
                        if($dataKey !== 'year' &&
                            $dataKey !== 'communeLabel' &&
                            $dataKey !== 'codeInsee' &&
                            $dataKey !== 'groupingType'
                        ) {
                            if(array_key_exists($dataKey, $aggregatedItem)) {
                                $aggregatedItem[$dataKey] += $datum;
                            }
                            else {
                                $aggregatedItem[$dataKey] = $datum;
                            }
                        }
                    }
                }
                $totals[$zipKey] = $aggregatedItem;
                $totals[$zipKey]['title'] =  $firstYear . '-' . $lastYear . ' Total : ' . $communeLabel;
                $totals[$zipKey]['communeLabel'] = $communeLabel;
                $totals[$zipKey]['codeInsee'] = $communeZipCode;
            }
            $departmentFinancials = [];
            $departmentFinancials['data'] = $zipCodes;
            $departmentFinancials['totals'] = $totals;


            // Area taxes total /an
            $zipCodes = [];
            foreach ($areaTaxes as $depKey => $areaTax) {
                $key = $areaTax['departmentLabel'];
                if(!array_key_exists($key, $zipCodes)) {
                    $zipCodes[$key] = [];
                }
                array_push($zipCodes[$key], $areaTax);
            }
            $totals = [];
            foreach ($zipCodes as $zipKey => $zipCode) {
                $aggregatedItem = [];
                $departmentLabel = $zipCode[0]['departmentLabel'];
                $lastYear = $zipCode[0]['year'];
                $firstYear = $zipCode[count($zipCode) - 1]['year'];

                foreach ($zipCode as $key => $data) {
                    foreach ($data as $dataKey => $datum) {
                        if($dataKey !== 'year' && $dataKey !== 'communeLabel'
                            && $dataKey !== 'codeInsee' && $dataKey !== 'departmentLabel'
                        ) {
                            if(array_key_exists($dataKey, $aggregatedItem)) {
                                $aggregatedItem[$dataKey] += $datum;
                            }
                            else {
                                $aggregatedItem[$dataKey] = $datum;
                            }
                        }
                    }
                }
                $totals[$zipKey] = $aggregatedItem;
                $totals[$zipKey]['title'] = $firstYear . '-' . $lastYear . ' Total : ' . $departmentLabel;
                $totals[$zipKey]['departmentLabel'] = $departmentLabel;
            }
            $areaTaxes = [];
            $areaTaxes['data'] = $zipCodes;
            $areaTaxes['totals'] = $totals;


            // Area financials total /an
            $zipCodes = [];
            foreach ($areaFinancials as $depKey => $areaFinancial) {
                $key = $areaFinancial['departmentLabel'];
                if(!array_key_exists($key, $zipCodes)) {
                    $zipCodes[$key] = [];
                }
                array_push($zipCodes[$key], $areaFinancial);
            }
            $totals = [];
            foreach ($zipCodes as $zipKey => $zipCode) {
                $aggregatedItem = [];
                $departmentLabel = $zipCode[0]['departmentLabel'];
                $lastYear = $zipCode[0]['year'];
                $firstYear = $zipCode[count($zipCode) - 1]['year'];

                foreach ($zipCode as $key => $data) {
                    foreach ($data as $dataKey => $datum) {
                        if($dataKey !== 'year' && $dataKey !== 'communeLabel'
                            && $dataKey !== 'codeInsee' && $dataKey !== 'departmentLabel' && $dataKey !== 'groupingType'
                        ) {
                            if(array_key_exists($dataKey, $aggregatedItem)) {
                                $aggregatedItem[$dataKey] += $datum;
                            }
                            else {
                                $aggregatedItem[$dataKey] = $datum;
                            }
                        }
                    }
                }
                $totals[$zipKey] = $aggregatedItem;
                $totals[$zipKey]['title'] = $firstYear . '-' . $lastYear . ' Total : ' . $departmentLabel;
                $totals[$zipKey]['departmentLabel'] = $departmentLabel;
            }
            $areaFinancials = [];
            $areaFinancials['data'] = $zipCodes;
            $areaFinancials['totals'] = $totals;

            $taxAccountingData['communeTaxes'] = $communeTaxes;
            $taxAccountingData['communeFinancials'] = $communeFinancials;

            $taxAccountingData['departmentTaxes'] = $departmentTaxes;
            $taxAccountingData['departmentFinancials'] = $departmentFinancials;

            $taxAccountingData['areaTaxes'] = $areaTaxes;
            $taxAccountingData['areaFinancials'] = $areaFinancials;
        }
        elseif($class === 'App\Entity\RegionalAdvisor') {
            $areaTaxes = $taxRepository->getAreaTaxByArea($electedMember->getAreaLabel());
            $areaFinancials = $accountingRepository->getAreaAccountingByArea($electedMember->getAreaLabel());

            // Area taxes total /an
            $zipCodes = [];
            foreach ($areaTaxes as $depKey => $areaTax) {
                $key = $areaTax['departmentLabel'];
                if(!array_key_exists($key, $zipCodes)) {
                    $zipCodes[$key] = [];
                }
                array_push($zipCodes[$key], $areaTax);
            }
            $totals = [];
            foreach ($zipCodes as $zipKey => $zipCode) {
                $aggregatedItem = [];
                $departmentLabel = $zipCode[0]['departmentLabel'];
                $lastYear = $zipCode[0]['year'];
                $firstYear = $zipCode[count($zipCode) - 1]['year'];

                foreach ($zipCode as $key => $data) {
                    foreach ($data as $dataKey => $datum) {
                        if($dataKey !== 'year' && $dataKey !== 'communeLabel'
                            && $dataKey !== 'codeInsee' && $dataKey !== 'departmentLabel'
                        ) {
                            if(array_key_exists($dataKey, $aggregatedItem)) {
                                $aggregatedItem[$dataKey] += $datum;
                            }
                            else {
                                $aggregatedItem[$dataKey] = $datum;
                            }
                        }
                    }
                }
                $totals[$zipKey] = $aggregatedItem;
                $totals[$zipKey]['title'] = $firstYear . '-' . $lastYear . ' Total : ' . $departmentLabel ;
                $totals[$zipKey]['departmentLabel'] = $departmentLabel;
            }
            $areaTaxes = [];
            $areaTaxes['data'] = $zipCodes;
            $areaTaxes['totals'] = $totals;


            // Area financials total /an
            $zipCodes = [];
            foreach ($areaFinancials as $depKey => $areaFinancial) {
                $key = $areaFinancial['departmentLabel'];
                if(!array_key_exists($key, $zipCodes)) {
                    $zipCodes[$key] = [];
                }
                array_push($zipCodes[$key], $areaFinancial);
            }
            $totals = [];
            foreach ($zipCodes as $zipKey => $zipCode) {
                $aggregatedItem = [];
                $departmentLabel = $zipCode[0]['departmentLabel'];
                $lastYear = $zipCode[0]['year'];
                $firstYear = $zipCode[count($zipCode) - 1]['year'];

                foreach ($zipCode as $key => $data) {
                    foreach ($data as $dataKey => $datum) {
                        if($dataKey !== 'year' && $dataKey !== 'communeLabel'
                            && $dataKey !== 'codeInsee' && $dataKey !== 'departmentLabel' && $dataKey !== 'groupingType'
                        ) {
                            if(array_key_exists($dataKey, $aggregatedItem)) {
                                $aggregatedItem[$dataKey] += $datum;
                            }
                            else {
                                $aggregatedItem[$dataKey] = $datum;
                            }
                        }
                    }
                }
                $totals[$zipKey] = $aggregatedItem;
                $totals[$zipKey]['title'] = $firstYear . '-' . $lastYear . ' Total : ' . $departmentLabel;
                $totals[$zipKey]['departmentLabel'] = $departmentLabel;
            }
            $areaFinancials = [];
            $areaFinancials['data'] = $zipCodes;
            $areaFinancials['totals'] = $totals;

            $taxAccountingData['areaTaxes'] = $areaTaxes;
            $taxAccountingData['areaFinancials'] = $areaFinancials;
        }
        else {
            $departmentTaxes = $taxRepository->getDepartmentTaxByDepartment($electedMember->getDepartmentLabel());
            $areaTaxes = $taxRepository->getDepartmentTaxByArea($electedMember->getAreaLabel());
            $departmentFinancials = $accountingRepository->getDepartmentAccountingByDepartment($electedMember->getDepartmentLabel());
            $areaFinancials = $accountingRepository->getDepartmentAccountingByArea($electedMember->getAreaLabel());


            $taxAccountingData = [];
            // Department taxes total /an
            $zipCodes = [];
            foreach ($departmentTaxes as $depKey => $departmentTax) {
                $key = $departmentTax['codeInsee'] . '-' . $departmentTax['communeLabel'];
                if(!array_key_exists($key, $zipCodes)) {
                    $zipCodes[$key] = [];
                }
                array_push($zipCodes[$key], $departmentTax);
            }
            $totals = [];
            foreach ($zipCodes as $zipKey => $zipCode) {
                $aggregatedItem = [];
                $communeLabel = $zipCode[0]['communeLabel'];
                $communeZipCode = $zipCode[0]['codeInsee'];
                $lastYear = $zipCode[0]['year'];
                $firstYear = $zipCode[count($zipCode) - 1]['year'];

                foreach ($zipCode as $key => $data) {
                    foreach ($data as $dataKey => $datum) {
                        if($dataKey !== 'year' && $dataKey !== 'communeLabel' && $dataKey !== 'codeInsee') {
                            if(array_key_exists($dataKey, $aggregatedItem)) {
                                $aggregatedItem[$dataKey] += $datum;
                            }
                            else {
                                $aggregatedItem[$dataKey] = $datum;
                            }
                        }
                    }
                }
                $totals[$zipKey] = $aggregatedItem;
                $totals[$zipKey]['title'] =  $firstYear . '-' . $lastYear . ' Total : ' . $communeLabel;
                $totals[$zipKey]['communeLabel'] = $communeLabel;
                $totals[$zipKey]['codeInsee'] = $communeZipCode;
            }
            $departmentTaxes = [];
            $departmentTaxes['data'] = $zipCodes;
            $departmentTaxes['totals'] = $totals;


            // Department financials total /an
            $zipCodes = [];
            foreach ($departmentFinancials as $depKey => $departmentFinancial) {
                $key = $departmentFinancial['codeInsee'] . '-' . $departmentFinancial['communeLabel'];
                if(!array_key_exists($key, $zipCodes)) {
                    $zipCodes[$key] = [];
                }
                array_push($zipCodes[$key], $departmentFinancial);
            }
            $totals = [];
            foreach ($zipCodes as $zipKey => $zipCode) {
                $aggregatedItem = [];
                $communeLabel = $zipCode[0]['communeLabel'];
                $communeZipCode = $zipCode[0]['codeInsee'];
                $lastYear = $zipCode[0]['year'];
                $firstYear = $zipCode[count($zipCode) - 1]['year'];

                foreach ($zipCode as $key => $data) {
                    foreach ($data as $dataKey => $datum) {
                        if($dataKey !== 'year' &&
                            $dataKey !== 'communeLabel' &&
                            $dataKey !== 'codeInsee' &&
                            $dataKey !== 'groupingType'
                        ) {
                            if(array_key_exists($dataKey, $aggregatedItem)) {
                                $aggregatedItem[$dataKey] += $datum;
                            }
                            else {
                                $aggregatedItem[$dataKey] = $datum;
                            }
                        }
                    }
                }
                $totals[$zipKey] = $aggregatedItem;
                $totals[$zipKey]['title'] =  $firstYear . '-' . $lastYear . ' Total : ' . $communeLabel;
                $totals[$zipKey]['communeLabel'] = $communeLabel;
                $totals[$zipKey]['codeInsee'] = $communeZipCode;

            }
            $departmentFinancials = [];
            $departmentFinancials['data'] = $zipCodes;
            $departmentFinancials['totals'] = $totals;


            // Area taxes total /an
            $zipCodes = [];
            foreach ($areaTaxes as $depKey => $areaTax) {
                $key = $areaTax['departmentLabel'];
                if(!array_key_exists($key, $zipCodes)) {
                    $zipCodes[$key] = [];
                }
                array_push($zipCodes[$key], $areaTax);
            }
            $totals = [];
            foreach ($zipCodes as $zipKey => $zipCode) {
                $aggregatedItem = [];
                $departmentLabel = $zipCode[0]['departmentLabel'];
                $lastYear = $zipCode[0]['year'];
                $firstYear = $zipCode[count($zipCode) - 1]['year'];

                foreach ($zipCode as $key => $data) {
                    foreach ($data as $dataKey => $datum) {
                        if($dataKey !== 'year' && $dataKey !== 'communeLabel'
                            && $dataKey !== 'codeInsee' && $dataKey !== 'departmentLabel'
                        ) {
                            if(array_key_exists($dataKey, $aggregatedItem)) {
                                $aggregatedItem[$dataKey] += $datum;
                            }
                            else {
                                $aggregatedItem[$dataKey] = $datum;
                            }
                        }
                    }
                }
                $totals[$zipKey] = $aggregatedItem;
                $totals[$zipKey]['title'] = $firstYear . '-' . $lastYear . ' Total : ' . $departmentLabel;
                $totals[$zipKey]['departmentLabel'] = $departmentLabel;
            }
            $areaTaxes = [];
            $areaTaxes['data'] = $zipCodes;
            $areaTaxes['totals'] = $totals;


            // Area financials total /an
            $zipCodes = [];
            foreach ($areaFinancials as $depKey => $areaFinancial) {
                $key = $areaFinancial['departmentLabel'];
                if(!array_key_exists($key, $zipCodes)) {
                    $zipCodes[$key] = [];
                }
                array_push($zipCodes[$key], $areaFinancial);
            }
            $totals = [];
            foreach ($zipCodes as $zipKey => $zipCode) {
                $aggregatedItem = [];
                $departmentLabel = $zipCode[0]['departmentLabel'];
                $lastYear = $zipCode[0]['year'];
                $firstYear = $zipCode[count($zipCode) - 1]['year'];

                foreach ($zipCode as $key => $data) {
                    foreach ($data as $dataKey => $datum) {
                        if($dataKey !== 'year' && $dataKey !== 'communeLabel'
                            && $dataKey !== 'codeInsee' && $dataKey !== 'departmentLabel' && $dataKey !== 'groupingType'
                        ) {
                            if(array_key_exists($dataKey, $aggregatedItem)) {
                                $aggregatedItem[$dataKey] += $datum;
                            }
                            else {
                                $aggregatedItem[$dataKey] = $datum;
                            }
                        }
                    }
                }
                $totals[$zipKey] = $aggregatedItem;
                $totals[$zipKey]['title'] = $firstYear . '-' . $lastYear . ' Total : ' . $departmentLabel;
                $totals[$zipKey]['departmentLabel'] = $departmentLabel;
            }
            $areaFinancials = [];
            $areaFinancials['data'] = $zipCodes;
            $areaFinancials['totals'] = $totals;

            $taxAccountingData['departmentTaxes'] = $departmentTaxes;
            $taxAccountingData['departmentFinancials'] = $departmentFinancials;
            $taxAccountingData['areaTaxes'] = $areaTaxes;
            $taxAccountingData['areaFinancials'] = $areaFinancials;
        }

        return $taxAccountingData;
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
                                      RegionalAdvisorRepository $regionalAdvisorRepository,
                                      CorsicanAdvisorRepository $corsicanAdvisorRepository,
                                      EuroDeputyRepository $euroDeputyRepository,
                                      DeputyRepository $deputyRepository,
                                      MayorRepository $mayorRepository,
                                      SenatorRepository $senatorRepository)
    {
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
            case 10:
                $electedMember = $departmentalAdvisorRepository->find($id);
                break;
            case 11:
                $electedMember = $regionalAdvisorRepository->find($id);
                break;
            default:
                break;
        }
        return $electedMember ?? null;
    }


    /**
     * @param int $categoryId
     * @return array
     */
    private function getElectedMembers(int $categoryId)
    {

        $em = $this->getDoctrine()->getManager();
        $data = null;
        switch ($categoryId) {
            case 1:
                $dql = "SELECT ma FROM App\Entity\MunicipalAdvisor ma";
                $data['query'] = $em->createQuery($dql);
                $data['title'] = 'Liste des conseillers municipaux';
                break;
            case 2:
                $dql = "SELECT ca FROM App\Entity\CommunityAdvisor ca";
                $data['query'] = $em->createQuery($dql);
                $data['title'] = 'Liste des conseillers communautaires';
                break;
            case 3:
                $dql = "SELECT da FROM App\Entity\DepartmentalAdvisor da";
                $data['query'] = $em->createQuery($dql);
                $data['title'] = 'Liste des conseillers départementaux';
                break;
            case 4:
                $dql = "SELECT ra FROM App\Entity\RegionalAdvisor ra";
                $data['query'] = $em->createQuery($dql);
                $data['title'] = 'Liste des conseillers(ères) régionaux';
                break;
            case 5:
                $dql = "SELECT ca FROM App\Entity\CorsicanAdvisor ca";
                $data['query'] = $em->createQuery($dql);
                $data['title'] = 'Liste des conseillers Corse';
                break;
            case 6:
                $dql = "SELECT ed FROM App\Entity\EuroDeputy ed";
                $data['query'] = $em->createQuery($dql);
                $data['title'] = 'Liste des députés européens';
                break;
            case 7:
                $dql = "SELECT s FROM App\Entity\Senator s";
                $data['query'] = $em->createQuery($dql);
                $data['title'] = 'Liste des sénarteurs';
                break;
            case 8:
                $dql = "SELECT d FROM App\Entity\Deputy d";
                $data['query'] = $em->createQuery($dql);
                $data['title'] = 'Liste des députés';
                break;
            case 9:
                $dql = "SELECT m FROM App\Entity\Mayor m";
                $data['query'] = $em->createQuery($dql);
                $data['title'] = 'Liste des maires';
                break;
            case 10:
                $dql = "SELECT dp FROM App\Entity\DepartmentalAdvisor dp where dp.functionLabel = 'Président du conseil départemental'";
                $data['query'] = $em->createQuery($dql);
                $data['title'] = 'Liste des présidents départementaux';
                break;
            case 11:
                $dql = "SELECT rp FROM App\Entity\RegionalAdvisor rp where rp.functionLabel = 'Président du conseil régional'";
                $data['query'] = $em->createQuery($dql);
                $data['title'] = 'Liste des présidents régionaux';
                break;
            default:
                break;
        }
        return $data;
    }

    /**
     * @param $electedMember
     * @param int $categoryId
     * @return array
     */
    private function getFormTypeByClassName($electedMember, int $categoryId): array
    {

        $class = get_class($electedMember);

        if ($class === 'App\Entity\Mayor' || $categoryId === 10 || $categoryId === 11) {
            $type['type'] = MPDPRNoteType::class;
            $note = new MPDPRNote();

            if ($categoryId === 10) {
                $note->setDepartmentalPresident($electedMember);
            } elseif ($categoryId === 11) {
                $note->setRegionalPresident($electedMember);
            } else {
                $note->setMayor($electedMember);
            }
            $type['data'] = $note;
            $type['name'] = 'MPDPRNoteType';
        } elseif ($class === 'App\Entity\Deputy') {
            $type['type'] = DeputyNoteType::class;
            $note = new DeputyNote();
            $note->setDeputy($electedMember);
            $type['data'] = $note;
            $type['name'] = 'DeputyNoteType';
        } elseif ($class === 'App\Entity\EuroDeputy') {
            $type['type'] = EuroDeputyNoteType::class;
            $note = new EuroDeputyNote();
            $note->setEuroDeputy($electedMember);
            $type['data'] = $note;
            $type['name'] = 'EuroDeputyNoteType';
        } else {
            $type['type'] = OtherNoteType::class;
            $note = new OtherNote();
            if ($class === 'App\Entity\Senator') {
                $note->setSenator($electedMember);
            } elseif ($class === 'App\Entity\MunicipalAdvisor') {
                $note->setMunicipalAdvisor($electedMember);
            } elseif ($class === 'App\Entity\CommunityAdvisor') {
                $note->setCommunityAdvisor($electedMember);
            } elseif ($class === 'App\Entity\CorsicanAdvisor') {
                $note->setCorsicanAdvisor($electedMember);
            }
            $type['data'] = $note;
            $type['name'] = 'OtherNoteType';
        }
        return $type;
    }

    /**
     * @param int $categoryId
     * @return string
     */
    private function getListName(int $categoryId)
    {
        switch ($categoryId) {
            case 1:
                $listName = 'liste-des-conseillers-municipaux';
                break;
            case 2:
                $listName = 'liste-des-conseillers-communautaires';
                break;
            case 3:
                $listName = 'liste-des-conseillers-departementaux';
                break;
            case 4:
                $listName = 'liste-des-conseillers-regionaux';
                break;
            case 5:
                $listName = 'liste-des-conseillers-corse';
                break;
            case 6:
                $listName = 'liste-des-deputes-europeens';
                break;
            case 7:
                $listName = 'liste-des-senateurs';
                break;
            case 8:
                $listName = 'liste-des-deputes';
                break;
            case 9:
                $listName = 'liste-des-maires';
                break;
            case 10:
                $listName = 'liste-des-presidents-des-departements';
                break;
            default:
                $listName = 'liste-des-presidents-des-regions';
                break;
        }
        return $listName;
    }

    /**
     * @return array
     */
    private function getNoteLinks()
    {
        $links = [];
        $titles = [
            'Noter un conseiller municipal',
            'Noter un conseiller communautaire',
            'Noter un conseiller départemental',
            'Noter un conseiller régional',
            'Noter un conseiller corse',
            'Noter un député européen',
            'Noter un sénateur',
            'Noter un député',
            'Noter un maire',
            'Noter un président de département',
            'Noter un président de région',
        ];

        $classes = [
            'badge-warning',
            'badge-primary',
            'badge-dark',
            'badge-secondary',
            'badge-warning',
            'badge-success',
            'badge-warning',
            'badge-light',
            'badge-danger',
            'badge-dark',
            'badge-success'
        ];


        $styles = [
            '',
            '',
            '',
            '',
            '#F5871F',
            '',
            '#7F00FF',
            '#77B5FE',
            '',
            '',
            '#916319'
        ];

        for ($i = 0; $i < count($titles); $i++) {
            array_push($links, [
                'title' => $titles[$i],
                'url' => $this->generateUrl('elected_members_list', ['categoryName' => $this->getListName($i + 1)]),
                'categoryId' => $i + 1,
                'class' => $classes[$i],
                'color' => $styles[$i]
            ]);
        }
        return $links;
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
}
