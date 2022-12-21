<?php

declare(strict_types=1);

namespace Helhum\SentryTypo3\Integration;

use Sentry\Event;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Information\Typo3Version;

class Typo3Context implements ContextInterface
{
    public function appliesToEvent(Event $event): bool
    {
        return true;
    }

    public function addToEvent(Event $event): void
    {
        $applicationType = ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'] ?? ServerRequestFactory::fromGlobals());
        $event->setTags(array_merge_recursive($event->getTags(), [
            'typo3_version' => (new Typo3Version())->getVersion(),
            'typo3_is_frontend' => $applicationType->isFrontend(),
            'typo3_is_backend' => $applicationType->isBackend(),
            'application_context' => (string)Environment::getContext(),
            'application_version' => \Composer\InstalledVersions::getPrettyVersion(\Composer\InstalledVersions::getRootPackage()['name']),
        ]));
    }
}
