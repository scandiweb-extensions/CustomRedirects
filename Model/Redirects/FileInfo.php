<?php

namespace ScandiPWA\CustomRedirects\Model\Redirects;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;

/**
 * Class FileInfo
 *
 * Provides information about requested file
 */
class FileInfo
{
    /**
     * Path in /pub/media directory
     */
    const ENTITY_MEDIA_PATH = 'tmp/redirects';

    /**
     * @var Filesystem $filesystem
     */
    private $filesystem;

    /**
     * @var Mime $mime
     */
    private $mime;

    /**
     * @var WriteInterface $mediaDirectory
     */
    private $mediaDirectory;

    /**
     * @param Filesystem $filesystem
     * @param Mime $mime
     */
    public function __construct(
        Filesystem $filesystem,
        Mime $mime
    ) {
        $this->filesystem = $filesystem;
        $this->mime = $mime;
    }

    /**
     * Get WriteInterface instance
     *
     * @return WriteInterface
     * @throws FileSystemException
     */
    private function getMediaDirectory()
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }

        return $this->mediaDirectory;
    }

    /**
     * Retrieve MIME type of requested file
     *
     * @param string $fileName
     * @return string
     * @throws FileSystemException
     */
    public function getMimeType($fileName)
    {
        $filePath = sprintf(
            '%s/%s',
            self::ENTITY_MEDIA_PATH,
            ltrim($fileName, '/')
        );
        $absoluteFilePath = $this->getMediaDirectory()->getAbsolutePath($filePath);
        $result = $this->mime->getMimeType($absoluteFilePath);

        return $result;
    }

    /**
     * Get file statistics data
     *
     * @param string $fileName
     * @return array
     * @throws FileSystemException
     */
    public function getStat($fileName)
    {
        $filePath = sprintf(
            '%s/%s',
            self::ENTITY_MEDIA_PATH,
            ltrim($fileName, '/')
        );
        $result = $this->getMediaDirectory()->stat($filePath);

        return $result;
    }

    /**
     * Check if the file exists
     *
     * @param string $fileName
     * @return bool
     * @throws FileSystemException
     */
    public function isExist($fileName, $baseTmpPath = false)
    {
        $filePath = sprintf(
            '%s/%s',
            self::ENTITY_MEDIA_PATH,
            ltrim($fileName, '/')
        );
        if ($baseTmpPath) {
            $filePath = sprintf(
                '%s/%s',
                $baseTmpPath,
                ltrim($fileName, '/')
            );
        }

        return $this->getMediaDirectory()->isExist($filePath);
    }

    /**
     * Delete the file
     *
     * @param string $fileName
     * @param string $baseTmpPath
     * @return bool
     * @throws FileSystemException
     */
    public function deleteFile($fileName, $baseTmpPath = '')
    {
        $filePath = sprintf(
            '%s/%s',
            self::ENTITY_MEDIA_PATH,
            ltrim($fileName, '/')
        );
        if ($baseTmpPath) {
            $filePath = sprintf(
                '%s/%s',
                $baseTmpPath,
                ltrim($fileName, '/')
            );
        }

        return $this->getMediaDirectory()->delete($filePath);
    }
}
