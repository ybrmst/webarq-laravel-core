<?php

namespace Webarq\Model;


class PageModel extends AbstractListingModel
{
    protected $table = 'pages';

    public function getDataSection($sectionId)
    {
        return $this->selectTranslate('title', 'intro', 'description')
                ->whereSectionId($sectionId)
                ->get();
    }
}