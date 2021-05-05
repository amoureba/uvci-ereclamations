<?php

namespace App\Controller\Admin;

use App\Repository\ClaimRepository;
use App\Service\StatsService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/dashbord", name="admin")
     */
    public function index(ClaimRepository $claimRepository, StatsService $stats): Response
    {
        /** BEGIN STATS */
        $statistics = $stats->getStats();
        /** END STATS */

        $examsClaims = $claimRepository->findBy(['category' => 'EXAMEN']);
        $devClaims = $claimRepository->findBy(['category' => 'DEVOIR']);
        $others = $claimRepository->findBy(['category' => 'AUTRE']);
        /*$techClaims = $claimRepository->findBy(['category' => 'TECHNIQUE']);*/

        return $this->render('admin/index.html.twig', [
            'statistics' => $statistics,
            'exams_claims' => $examsClaims,
            'dev_claims' => $devClaims,
            'others' => $others,
            /*'tech_claims' => $techClaims*/
        ]);
    }
}
