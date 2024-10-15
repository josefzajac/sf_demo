<?php declare(strict_types=1);

namespace Document\Modules\File\DTO\Request;

use DateTime;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

readonly class FileRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property]
        public ?string $name = null,
        #[Assert\Date]
        #[OA\Property]
        public ?string $date = null,
        /**
         * @var array<mixed> $file
         */
        #[OA\Property(type: 'byte[]', example: '[137,80,78,71,13,10,26,10,0,0,0,13,73,72,68,82,0,0,0,8,0,0,0,8,1,3,0,0,0,254,193,44,200,0,0,0,6,80,76,84,69,255,255,255,191,191,191,163,67,118,57,0,0,0,14,73,68,65,84,8,215,99,248,0,133,252,16,8,0,46,0,3,253,163,105,110,209,0,0,0,0,73,69,78,68,174,66,96,130]')]
        public ?array $file = null,
    ) {}

    public function getName(): string
    {
        return (string) $this->name;
    }

    public function getDate(): string
    {
        /** @var DateTime $date */
        $date = DateTime::createFromFormat('Y-m-d', (string) $this->date);

        return $date->format('Y-m');
    }

    /**
     * @return array<mixed>
     */
    public function getFile(): array
    {
        return (array) $this->file;
    }
}
