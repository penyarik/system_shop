<?php

namespace App\Service;

use App\CustomEntity\FileType;
use App\Entity\File;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{
    public const IMAGE_PATH = 'upload/admin/image';
    public const ATTACHMENT_PATH = 'upload/admin/attachment';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FileRepository $fileRepository,
    )
    {
    }

    public function saveFile(int $entityId, array $files, FileType $fileType, string $filePath): void
    {
        /**
         * @var UploadedFile $file
         */
        foreach ($files as $file) {
            $fileName = $this->getFileName($file);
            $file->move($filePath, $fileName);

            $file = new File();
            $file->setName($fileName)
                ->setPath($filePath)
                ->setEntityName($fileType->name)
                ->setEntityId($entityId);

            $this->entityManager->persist($file);
            $this->entityManager->flush();
        }
    }

    public function updateFile(int $entityId, array $filesNew, FileType $fileType, string $filePath): void
    {
        $files = $this->fileRepository->findByEntityIdAndEntityName($entityId, $fileType->name);

        $this->remove($files);

        $this->saveFile($entityId, $filesNew, $fileType, $filePath);
    }

    public function removeFiles(int $entityId, FileType $entityType): void
    {
        $this->remove($this->fileRepository->findByEntityIdAndEntityName($entityId, $entityType->name));
    }

    private function remove(array $files): void
    {
        /**
         * @var File $file
         */
        foreach ($files as $file) {
            $this->fileRepository->remove($file);

            try {
                unlink($file->getPath() . '/' . $file->getName());
            } catch (\Throwable $exception) {
                continue;
            }
        }
    }

    private function getFileName(UploadedFile $file): string
    {
        $file = explode('.', $file->getClientOriginalName());
        return $file[0] . '_' . time() . '.' . $file[1];
    }
}