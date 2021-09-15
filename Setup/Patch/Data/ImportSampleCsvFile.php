<?php

namespace ScandiPWA\CustomRedirects\Setup\Patch\Data;

use Eselo\Migration\Helper\MediaMigration;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class ImportSampleCsvFile implements DataPatchInterface
{
    /**
     * @var MediaMigration;
     */
    protected $mediaMigration;

    /**
     * AddContactUsImages constructor.
     * @param MediaMigration $mediaMigration
     */
    public function __construct(MediaMigration $mediaMigration)
    {
        $this->mediaMigration = $mediaMigration;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return DataPatchInterface|void
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function apply()
    {
        $mediaFiles = [
            '/sample/samplefile.csv'
        ];

        $this->mediaMigration->copyMediaFiles(
            $mediaFiles,
            'ScandiPWA_CustomRedirects',
            'csv'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }
}
