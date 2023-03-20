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
    // Отображение базовой страницы
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }
    // Получение списка резюме
    #[Route('/api/cv', name: 'get_resume_list', methods: 'GET')]
    public function getResumeList(ResumeRepository $resumeRepository): JsonResponse
    {
        $resumeList = $resumeRepository->findAll();

        return new JsonResponse(
            $this->serializer->serialize($resumeList, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['education']]),
            json: true
        );
    }
    // Получение резюме
    // на вход id искомого резюме
    #[Route('/api/cv/{id}', name: 'get_resume', methods: 'GET')]
    public function getResume(int $id, ResumeRepository $resumeRepository, EducationRepository $educationRepository) : JsonResponse
    {
        $resume = $resumeRepository->find($id);
        if(is_null($resume)) return new JsonResponse(['error' => 'Резюме не найдено']);
        $educations = $educationRepository->findBy(['resume' => $resume->getId()]);
        return new JsonResponse([
            'education' => $this->serializer->normalize($educations, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['resume']]),
            'result' => $this->serializer->normalize($resume, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['education']]),
        ]);
    }
    // Добавление новой записи
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

        foreach ($resumeData['education']['secondEducation'] as $parameter) {
            $education = new Education();
            $education = $educationRepository->CreateEducation($education, $parameter, $resume);
            $educationRepository->save($education, true);
        }

        return new JsonResponse([
            'resumeId' => $resume->getId(),
            'resumeStatus' => $resume->getStatus(),
        ]);
    }
    // Редактирование записи
    // на вход id редактируемой записи
    #[Route('/api/cv/{id}/edit', name: 'edit_resume', methods: 'POST')]
    public function editResume(int $id, Request $request, ResumeRepository $resumeRepository, EducationRepository $educationRepository) : JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);
        $resume = $resumeRepository->find($id);
        if(is_null($resume)) return new JsonResponse(['error' => 'Резюме не найдено']);
        $resume = $resumeRepository->CreateResume($resume, $parameters);
        $resumeRepository->save($resume, true);

        $educations = $educationRepository->findBy(['resume' => $resume->getId()]);

        foreach ($educations as $education) {
            $educationRepository->remove($education, true);
        }

        $education = new Education();
        $education = $educationRepository->CreateEducation($education, $parameters['education'], $resume);
        $educationRepository->save($education, true);
        $resume->addEducation($education);

        foreach ($parameters['education']['secondEducation'] as $parameter) {
            $education = new Education();
            $education = $educationRepository->CreateEducation($education, $parameter, $resume);
            $educationRepository->save($education, true);
        }

        return new JsonResponse(['result' => $resume->getId()]);
    }
    // Обновление статуса резюме
    // на вход id обновляемого резюме
    #[Route('/api/cv/{id}/status/update', name: 'status_update', methods: 'POST')]
    public function changeResumeStatus(int $id, Request $request, ResumeRepository $resumeRepository) : JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);
        $resume = $resumeRepository->find($id);
        $resume->setStatus($parameters['Status']);
        $resumeRepository->save($resume, true);
        return new JsonResponse([
            'result' => $resume->getId(),
        ]);
    }
}
