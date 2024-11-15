<?php

namespace App\Controller;

use App\Application\Documentation\EvidenceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EvidenceController extends AbstractController
{
    private EvidenceService $evidenceService;

    public function __construct(EvidenceService $evidenceService)
    {
        $this->evidenceService = $evidenceService;
    }

    #[Route('/evidence', methods: ['GET'])]
    public function listEvidence(): Response
    {
        $evidenceList = $this->evidenceService->listAllEvidence();
        return $this->json($evidenceList);
    }

    #[Route('/evidence/{id}', methods: ['GET'])]
    public function getEvidence(int $id): Response
    {
        $evidence = $this->evidenceService->getEvidenceById($id);

        if (!$evidence) {
            return $this->json(['error' => 'Evidence not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($evidence);
    }

    #[Route('/evidence', methods: ['POST'])]
    public function addEvidence(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title'], $data['description'], $data['dateCollected'])) {
            return $this->json(['error' => 'Invalid input'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $dateCollected = new \DateTimeImmutable($data['dateCollected']);
            $evidence = $this->evidenceService->addEvidence(
                $data['title'],
                $data['description'],
                $dateCollected
            );

            return $this->json($evidence, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to create evidence'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
