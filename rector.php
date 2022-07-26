<?php

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Symfony\Set\SensiolabsSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->symfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml');
    $rectorConfig->phpVersion(PhpVersion::PHP_81);
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->import(SetList::PHP_55);
    $rectorConfig->import(SetList::PHP_56);
    $rectorConfig->import(SetList::PHP_70);
    $rectorConfig->import(SetList::PHP_71);
    $rectorConfig->import(SetList::PHP_73);
    $rectorConfig->import(SetList::PHP_74);
    $rectorConfig->import(SetList::PHP_80);
    $rectorConfig->import(SetList::PHP_81);
    $rectorConfig->import(SymfonySetList::SYMFONY_54);
    $rectorConfig->import(SymfonySetList::SYMFONY_CODE_QUALITY);
    $rectorConfig->import(SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION);
    $rectorConfig->import(DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES);
    $rectorConfig->import(SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES);
    $rectorConfig->import(SensiolabsSetList::FRAMEWORK_EXTRA_61);
};
