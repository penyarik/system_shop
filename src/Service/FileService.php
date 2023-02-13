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

    public function saveFile(int $productId, array $files, bool $isGallery): void
    {
        /**
         * @var UploadedFile $file
         */
        foreach ($files as $file) {
            $fileName = $this->getFileName($file);
            $filePath = $isGallery ? getcwd() . '/' . self::IMAGE_PATH : getcwd() . '/' . self::ATTACHMENT_PATH;
            $file->move($filePath, $fileName);

            $file = new File();
            $file->setName($fileName)
                ->setPath($filePath)
                ->setEntityName($isGallery ? FileType::PRODUCT_GALLERY->name : FileType::PRODUCT_ATTACHMENT->name)
                ->setEntityId($productId);

            $this->entityManager->persist($file);
            $this->entityManager->flush();
        }
    }

    public function updateFile(int $productId, array $filesNew, bool $isGallery): void
    {
        $entityName = $isGallery ? FileType::PRODUCT_GALLERY->name : FileType::PRODUCT_ATTACHMENT->name;
        $files = $this->fileRepository->findByEntityIdAndEntityName($productId, $entityName);

        $this->remove($files);

        $this->saveFile($productId, $filesNew, $isGallery);
    }

    public function removeFiles(int $productId): void
    {
        $files = array_merge(
             $this->fileRepository->findByEntityIdAndEntityName($productId, FileType::PRODUCT_GALLERY->name),
             $this->fileRepository->findByEntityIdAndEntityName($productId, FileType::PRODUCT_ATTACHMENT->name)
        );

        $this->remove($files);
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