<?php

namespace Flageolett\eZCompletionBundle\Service;

use \eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use Flageolett\eZCompletionBundle\Abstracts\CompletionAbstract;
use Flageolett\eZCompletionBundle\Entity\Completion;
use Flageolett\eZCompletionBundle\Entity\CompletionContainer;

class ContentTypeServiceCompletion extends CompletionAbstract
{
    /** @var ContentTypeService */
    protected $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    protected function getDataSource()
    {
        return array(
            'contentType' => $this->fetchContentTypes(),
            'contentTypeGroup' => $this->fetchContentTypeGroups()
        );
    }

    /**
     * @return ContentType[]
     */
    public function getContentTypes()
    {
        $contentTypeGroups = $this->contentTypeService->loadContentTypeGroups();
        $contentTypes = array();
        foreach ($contentTypeGroups as $contentTypeGroup) {
            $contentTypes = array_merge(array_map(function(ContentType $contentType) {
                    return $contentType;
            }, $this->contentTypeService->loadContentTypes($contentTypeGroup)), $contentTypes);
        }

        return $contentTypes;
    }

    /**
     * @return array
     */
    public function fetchContentTypes()
    {
        return array_map(function(ContentType $contentType)
        {
            return array(
                'id' => (int)$contentType->id,
                'name' => self::getTranslatedName($contentType, $this->language),
                'identifier' => $contentType->identifier,
                'remoteId' => $contentType->remoteId
            );
        }, $this->getContentTypes());
    }

    public function fetchContentTypeGroups()
    {
        return array_map(function(ContentTypeGroup $contentTypeGroup)
        {
            return array(
                'id' => (int)$contentTypeGroup->id,
                'identifier' => $contentTypeGroup->identifier
            );
        }, $this->contentTypeService->loadContentTypeGroups());
    }
}
