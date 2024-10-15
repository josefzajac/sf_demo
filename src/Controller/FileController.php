<?php declare(strict_types=1);

namespace Document\Controller;

use Document\Modules\File\DTO\Request\FileRequestDTO;
use Document\Modules\File\DTO\Request\FileUpdateRequestDTO;
use Document\Modules\File\Service\FileService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('', 'api_')]
class FileController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly FileService $service,
        private readonly ValidatorInterface $validator,
    ) {}

    #[Route('/documents', name: 'upload', methods: ['POST'])]
    #[OA\Response(response: 201, description: 'File created or file exists')]
    #[OA\Response(response: 400, description: 'Validation error')]
    #[OA\Response(response: 403, description: 'Access denied')]
    #[OA\Response(response: 409, description: 'Other error')]
    public function upload(Request $request): Response
    {
        return $this->validateAndCall(
            FileRequestDTO::class,
            function (FileRequestDTO $dto) {
                $path = $this->service->uploadFile($dto);

                return new Response(status: Response::HTTP_CREATED, headers: ['location' => $path]);
            },
            $request,
        );
    }

    #[Route('/documents/{type}/{profile}/{date}/{filename}', name: 'put', methods: ['PUT'])]
    #[OA\Response(response: 204, description: 'File updated')]
    #[OA\Response(response: 400, description: 'Validation error')]
    #[OA\Response(response: 403, description: 'Access denied')]
    #[OA\Response(response: 404, description: 'File not found')]
    #[OA\Response(response: 409, description: 'Other error')]
    public function update(string $type, string $profile, string $date, string $filename, Request $request): Response
    {
        $path = join('/', [$type, $profile, $date, $filename]);

        return $this->validateAndCall(
            FileUpdateRequestDTO::class,
            function (FileUpdateRequestDTO $dto) use ($path) {
                $this->service->updateFile($dto, $path);

                return new Response(status: Response::HTTP_NO_CONTENT);
            },
            $request
        );
    }

    #[Route('/documents/{type}/{profile}/{date}/{filename}', name: 'delete', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'File deleted')]
    #[OA\Response(response: 400, description: 'Validation error')]
    #[OA\Response(response: 403, description: 'Access denied')]
    #[OA\Response(response: 404, description: 'File not found')]
    #[OA\Response(response: 409, description: 'Other error')]
    public function delete(string $type, string $profile, string $date, string $filename): Response
    {
        $path = join('/', [$type, $profile, $date, $filename]);
        $this->service->deleteFile($path);

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
