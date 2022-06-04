<?php

namespace App\Controller\Manager;

use App\Form\SettingsFormType;
use App\Repository\CommunityAdvisorRepository;
use App\Repository\CorsicanAdvisorRepository;
use App\Repository\DepartmentalAdvisorRepository;
use App\Repository\DeputyRepository;
use App\Repository\EuroDeputyRepository;
use App\Repository\MayorRepository;
use App\Repository\MunicipalAdvisorRepository;
use App\Repository\RegionalAdvisorRepository;
use App\Repository\SenatorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class DashboardController
 * @package App\Controller\manager
 * @Route("/manager")
 */
class DashboardController extends AbstractController
{
    /**
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
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @Route("/dashboard", name="manager")
     */
    public function index(MunicipalAdvisorRepository $municipalAdvisorRepository,
                          CommunityAdvisorRepository $communityAdvisorRepository,
                          DepartmentalAdvisorRepository $departmentalAdvisorRepository,
                          RegionalAdvisorRepository  $regionalAdvisorRepository,
                          CorsicanAdvisorRepository $corsicanAdvisorRepository,
                          EuroDeputyRepository $euroDeputyRepository,
                          DeputyRepository $deputyRepository,
                          MayorRepository $mayorRepository,
                          SenatorRepository $senatorRepository)
    {

        $municipalAdvisors = $municipalAdvisorRepository->getCount();
        $communityAdvisors = $communityAdvisorRepository->getCount();
        $departmentalAdvisors = $departmentalAdvisorRepository->getCount();
        $regionalAdvisors = $regionalAdvisorRepository->getCount();
        $corsicanAdvisors = $corsicanAdvisorRepository->getCount();
        $euroDeputies = $euroDeputyRepository->getCount();
        $deputies = $deputyRepository->getCount();
        $mayors = $mayorRepository->getCount();
        $senators = $senatorRepository->getCount();
        $departmentalPresidents = $departmentalAdvisorRepository->getPresidentsCount();
        $regionalPresidents = $regionalAdvisorRepository->getPresidentsCount();

        $depMunicipalAdvisors = $municipalAdvisorRepository->getMembersByDepartment();
        $depCommunityAdvisors = $communityAdvisorRepository->getMembersByDepartment();
        $depDepartmentalAdvisors = $departmentalAdvisorRepository->getMembersByDepartment();
        $depRegionalAdvisors = $regionalAdvisorRepository->getMembersByDepartment();
        $depSenators = $senatorRepository->getMembersByDepartment();
        $depDeputies = $deputyRepository->getMembersByDepartment();
        $depMayors = $mayorRepository->getMembersByDepartment();

        $regMunicipalAdvisors = $municipalAdvisorRepository->getMembersByArea();
        $regCommunityAdvisors = $communityAdvisorRepository->getMembersByArea();
        $regDepartmentalAdvisors = $departmentalAdvisorRepository->getMembersByArea();
        $regRegionalAdvisors = $regionalAdvisorRepository->getMembersByArea();
        $regSenators = $senatorRepository->getMembersByArea();
        $regDeputies = $deputyRepository->getMembersByArea();
        $regMayors = $mayorRepository->getMembersByArea();

        $depStats = $this->getStats([
            $depMunicipalAdvisors, $depCommunityAdvisors, $depDepartmentalAdvisors,
            $depRegionalAdvisors, $depSenators, $depDeputies, $depMayors
        ], 'departmentLabel');

        $areaStats = $this->getStats([
            $regMunicipalAdvisors, $regCommunityAdvisors, $regDepartmentalAdvisors,
            $regRegionalAdvisors, $regSenators, $regDeputies, $regMayors
        ], 'areaLabel');

        return $this->render('manager/dashboard/index.html.twig', [
            'municipalAdvisors' => $municipalAdvisors,
            'communityAdvisors' => $communityAdvisors,
            'departmentalAdvisors' => $departmentalAdvisors,
            'regionalAdvisors' => $regionalAdvisors,
            'departmentalPresidents' => $departmentalPresidents,
            'regionalPresidents' => $regionalPresidents,
            'corsicanAdvisors' => $corsicanAdvisors,
            'euroDeputies' => $euroDeputies,
            'deputies' => $deputies,
            'mayors' => $mayors,
            'senators' => $senators,
            'depStats' => $depStats,
            'areaStats' => $areaStats
        ]);
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @Route("/settings", name="manager_settings", methods={"GET","POST"})
     */
    public function settings(Request $request, UserPasswordEncoderInterface $encoder) {

        $manager = $this->getUser();
        $form = $this->createForm(SettingsFormType::class, $manager, ['method' => Request::METHOD_POST]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            if($form->get('password')->getData()) {
                $encoded = $encoder->encodePassword($manager, $form->get('password')->getData());
                $manager->setPassword($encoded);
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Compte mis à jour avec succès.');
        }

        return $this->render('manager/settings/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param array $allAdvisors
     * @param string $criteria
     * @return array
     */
    public function getStats(array $allAdvisors, string $criteria) {
        $stats['keys'] = [];
        $stats['counts'] = [];

        foreach ($allAdvisors as $advisors) {
            foreach ($advisors as $advisor) {
                if(in_array($advisor[$criteria], $stats['keys'])) {
                    $index = array_search($advisor[$criteria], $stats['keys']);
                    $stats['counts'][$index] += $advisor['count'];
                }
                else {
                    array_push($stats['keys'], $advisor[$criteria]);
                    array_push($stats['counts'], (int)$advisor['count']);
                }
            }
        }

        return $stats;
    }
}
