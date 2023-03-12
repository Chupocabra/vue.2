<?php

namespace App\Controller;

use App\Entity\Education;
use App\Entity\Resume;
use App\Repository\EducationRepository;
use App\Repository\ResumeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class MainController extends AbstractController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }

    #[Route('/api/cv', name: 'get_resume_list', methods: 'GET')]
    public function getResumeList(ResumeRepository $resumeRepository): JsonResponse
    {
        $resumeList = $resumeRepository->findAll();

        return new JsonResponse(
            $this->serializer->serialize($resumeList, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['education']]),
            json: true
        );
    }

    #[Route('/api/cv/{id}', name: 'get_resume', methods: 'GET')]
    public function getResume(int $id, ResumeRepository $resumeRepository, EducationRepository $educationRepository) : JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize(
                $resumeRepository->findOneBy(['id' => $id]),
                'json'
            ),
            json: true
        );
    }

    #[Route('/api/cv/add', name: 'add_resume', methods: 'POST')]
    public function addResume(Request $request, ResumeRepository $resumeRepository, EducationRepository $educationRepository) : JsonResponse
    {
        $resumeData = json_decode($request->getContent(), true);
        $resume = new Resume();
        $resume = $resumeRepository->CreateResume($resume, $resumeData);
        $resumeRepository->save($resume, true);
        $education = new Education();
        $education = $educationRepository->CreateEducation($education, $resumeData['education'], $resume);
        $educationRepository->save($education, true);

        return new JsonResponse([
            'resumeId' => $resume->getId(),
            'resumeStatus' => $resume->getStatus(),
        ]);
    }

    #[Route('/api/cv/{id}/edit', name: 'edit_resume', methods: 'POST')]
    public function editResume() : JsonResponse
    {
        return JsonResponse::fromJsonString('post');
    }

    #[Route('/api/cv/{id}/status/update', name: 'status_update', methods: 'POST')]
    public function changeResumeStatus(int $id, Request $request, ResumeRepository $resumeRepository) : JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);
        $resume = $resumeRepository->find($id);
        $resume->setStatus($parameters['status']);
        $resumeRepository->save($resume, true);
        return new JsonResponse([
            'result' => $resume->getId(),
        ]);
    }
}
