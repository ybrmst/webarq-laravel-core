<?php

namespace Webarq\Model;


class SectionModel extends AbstractListingModel
{
    protected $table = 'sections';

    /**
     * @param string $template
     * @return array|mixed
     */
    public function getTemplateSection($template)
    {
        return $this->select('object as key', 'id')
                ->whereTemplate($template)
                ->orderBy('sequence', 'asc')
                ->get()
                ->toArray();
    }
}