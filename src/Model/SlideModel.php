<?php

namespace Webarq\Model;


class SlideModel extends AbstractListingModel
{
    protected $table = 'slides';

    /**
     * @param $sectionId
     * @return mixed
     */
    public function getDataSection($sectionId)
    {
        return $this->select('path', 'path_tab', 'path_mobile', 'permalink')
                ->selectTranslate('title', true)
                ->whereSectionId($sectionId)
                ->get();
    }
}