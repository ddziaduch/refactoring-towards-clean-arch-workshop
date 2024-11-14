<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Officer;
use App\Entity\EmergencyCall;

class EmergencyCallController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handleEmergencyCall(Request $request): Response
    {
        // Extract data from the request
        $callerName = $request->get('callerName');
        $location = $request->get('location');
        $emergencyDetails = $request->get('details');
        $priority = $request->get('priority');

        // Create new EmergencyCall record
        $emergencyCall = new EmergencyCall();
        $emergencyCall->setCallerName($callerName);
        $emergencyCall->setLocation($location);
        $emergencyCall->setDetails($emergencyDetails);
        $emergencyCall->setPriority($priority);
        $emergencyCall->setStatus('Pending');

        // Save the call record
        $this->entityManager->persist($emergencyCall);
        $this->entityManager->flush();

        // Assign the call to an officer (simplified logic)
        $officer = $this->entityManager->getRepository(Officer::class)->findOneBy(['status' => 'Available']);
        if ($officer) {
            $officer->setStatus('Assigned');
            $officer->setCurrentAssignment($emergencyCall);
            $this->entityManager->persist($officer);
            $this->entityManager->flush();
        } else {
            return new Response('No available officers.', 400);
        }

        // Return success message
        return new Response('Emergency call handled successfully.', 200);
    }
}
