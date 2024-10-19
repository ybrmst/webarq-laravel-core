<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 3/3/2017
 * Time: 3:24 PM
 */

namespace Webarq\Manager\Cms\HTML\Form\Input;


use DB;
use Wa;
use Webarq\Model\AbstractListingModel;

class SelectTemplateInputManager extends SelectInputManager
{
    /**
     * Activeness filter
     *
     * @var bool
     */
    protected $activeness = false;

    /**
     * @var
     */
    protected $section;

    /**
     * @var
     */
    protected $template;

    /**
     * @var int
     */
    protected $limit = 0;

    /**
     * @var bool
     */
    protected $blankOption = true;

    public function buildInput()
    {
        if (!is_bool($this->section)) {
            $this->limit = (int)$this->limit;
        }

        $this->options = [];

        $templates = config('webarq.template.names', []);

        if ([] !== $templates) {
            if (null === $this->section) {
                $this->templateOptions($templates);
            } elseif (true === $this->section) {
                $this->templateSectionOptions();
            } elseif (null !== ($model = Wa::model('section'))) {
                $rows = $this->dbRow($model);

                if ($rows->count()) {
                    foreach ($rows as $row) {
                        if ($this->isValidRow($row)) {
                            try {
                                $decode = html_entity_decode((string)$row->menu);
                            } catch (\Exception $e) {
                                $decode = (string)$row->menu;
                            }
                            $this->options[$row->menu_id . '.' . $row->id]
                                    = strip_tags($decode)
                                    . ' > ' . ($row->title ?: title_case($this->section));
                        }
                    }
                }
            }
        }

        return parent::buildInput();
    }

    protected function templateOptions(array $templates)
    {
        foreach ($templates as $key => $option) {
            $this->options[$key] = array_get($option, 'name', ucfirst($key));
        }
    }

    protected function templateSectionOptions()
    {
        $sections = config('webarq.template.sections');

        foreach ($sections as $key => $option) {
            $this->options[$key] = array_get($option, 'name', ucfirst($key));
        }
    }

    protected function dbRow(AbstractListingModel $model)
    {
        $select = $model->from('sections as s')
                ->select('s.id', 's.title', 'm.title as menu', 'm.id as menu_id', 's.object')
                ->join('menus as m', 'm.template', '=', 's.template')
                ->whereIn('object', (array)$this->section);

        if (isset($this->template)) {
            $select->whereIn('m.template', (array)$this->template);
        }

        if (true === $this->activeness) {
            if ('edit' === $this->formType) {
                $select->where(function ($builder) {
                    $builder->where('m.is_active', 1)
                            ->orWhere('m.id', substr($this->value, 0, strpos($this->value, '.')));
                });
            } else {
                $select->where('m.is_active', 1);
            }
        }

        return $select->get();
    }

    protected function isValidRow(AbstractListingModel $row)
    {
        $limit = (int)config('webarq.template.sections.' . $row->{'object'} . '.limit', $this->limit);

        if ($limit !== 0) {
            $count = DB::table($this->table->getName())
                    ->select('section_id')
                    ->where('section_id', $row->{'menu_id'} . '.' . $row->{'id'})
                    ->get();

            if ('edit' === $this->formType && $row->{'menu_id'} . '.' . $row->{'id'} === $this->value) {
                return $limit > $count->count() - 1;
            }

            return $limit > $count->count();
        }

        return true;
    }
}