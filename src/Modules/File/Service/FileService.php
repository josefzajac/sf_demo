<?php declare(strict_types=1);

namespace Document\Modules\File\Service;

use Document\Modules\File\DTO\Request\FileRequestDTO;
use Document\Modules\File\DTO\Request\FileUpdateRequestDTO;
use Document\Modules\File\Exception\FileNotFoundException;
use Document\Modules\File\Exception\UploadException;
use Document\Modules\File\Exception\ValidationException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class FileService
{
    public const TYPE_IMAGE = 'images';
    public const TYPE_DOCUMENT = 'documents';
    public const TYPE_OTHER = 'others';

    public function __construct(
        private readonly string $dataDir
    ) {}

    public function uploadFile(FileRequestDTO $dto): string
    {
        $filesystem = new Filesystem();

        if (count($dto->getFile()) === 0) {
            throw new ValidationException('file attribute: Is missing or empty.');
        }
        $dir = $this->getOriginalPath($this->getFileType($dto->getName()), $dto->getDate());
        $filename = $dto->getName();
        $fullPath = $dir . '/' . $filename;

        if ($filesystem->exists($fullPath)) {
            return $this->getPublicPath($fullPath);
        }

        $filesystem->mkdir(Path::normalize($dir));
        $image = implode(array_map('chr', $dto->getFile()));
        file_put_contents($fullPath, $image);

        if (!$filesystem->exists($fullPath)) {
            throw new UploadException('Upload failed');
        }

        return $this->getPublicPath($fullPath);
    }

    public function updateFile(FileUpdateRequestDTO $dto, string $path): string
    {
        $filesystem = new Filesystem();

        if (count($dto->getFile()) === 0) {
            throw new ValidationException('file attribute: Is missing or empty.');
        }

        $this->deleteFile($path);
        $fullPath = $this->dataDir . '/' . $path;

        $filesystem->mkdir(Path::normalize(basename($fullPath)));
        $image = implode(array_map('chr', $dto->getFile()));
        file_put_contents($fullPath, $image);

        if (!$filesystem->exists($fullPath)) {
            throw new UploadException('Upload failed');
        }

        return $this->getPublicPath($fullPath);
    }

    public function deleteFile(string $path): void
    {
        $filesystem = new Filesystem();
        $fullPath = $this->dataDir . '/' . $path;

        if (!$filesystem->exists($fullPath)) {
            throw new FileNotFoundException('File not exists');
        }

        // TODO: remove resizes
        $filesystem->remove($fullPath);

        if ($filesystem->exists($fullPath)) {
            throw new UploadException('Upload failed');
        }
    }

    private function getFileType(string $mimeType): string
    {
        return match (true) {
            str_ends_with($mimeType, '.bmp'),
            str_ends_with($mimeType, '.gif'),
            str_ends_with($mimeType, '.jpg'),
            str_ends_with($mimeType, '.jpeg'),
            str_ends_with($mimeType, '.png'),
            str_ends_with($mimeType, '.svg'),
            str_ends_with($mimeType, '.webp') => self::TYPE_IMAGE,
            str_ends_with($mimeType, '.pdf'),
            str_ends_with($mimeType, '.docx') => self::TYPE_DOCUMENT,
            default => self::TYPE_OTHER
        };
    }

    private function getPublicPath(string $path): string
    {
        return '/data/' . substr($path, strlen($this->dataDir) + 1);
    }

    private function getOriginalPath(string $fileType, string $date): string
    {
        return join('/', [$this->dataDir, $fileType, 'origin', $date]);
    }
}
