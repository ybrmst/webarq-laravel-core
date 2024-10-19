<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 3/29/2017
 * Time: 6:33 PM
 */

namespace Webarq\Http\Controllers\Panel\System;


use Request;
use Wa;
use Webarq\Http\Controllers\Panel\BaseController;

class MenusController extends BaseController
{
    public function actionGetSection()
    {
        $this->setLayout('menu-section');

        $id = $this->getParam(1);

        if (is_numeric($id) && null !== ($menu = Wa::model('menu')->whereId($id)->first())) {
            view()->share([
                    'shareModuleTitle' => $menu->title,
                    'sharePanelTitle' => $this->panel->getTitle() . ' Section'
            ]);

            $this->layout->with([
                    'menu' => $menu,
                    'sections' => $this->collectSection($menu->template, $id)
            ]);

// Register session redirect
            \Session::put('redirect-url', Request::fullUrl());
        } else {
            return $this->actionGetForbidden();
        }
    }

    /**
     * @param $template
     * @param $menuId
     * @return array
     */
    protected function collectSection($template, $menuId)
    {
        $sections = Wa::model('section')->getTemplateSection($template);

        if ([] !== $sections) {
            $data = [];
            foreach ($sections as $section) {
                $section += config('webarq.template.sections.' . array_get($section, 'key', 'unknown'), []);

                $section['id'] = $menuId . '.' . $section['id'];

                if (null !== ($panel = array_get($section, 'panel'))) {
                    foreach (Wa::modules() as $module) {
                        if (null !== ($module = Wa::module($module))
                                && null !== ($info = $module->getPanel($panel))
                                && Wa::panel()->isAccessible($module, $info)
                        ) {
                            $data[$panel] = [
                                    'option' => $section,
                                    'title' => $info->getTitle(),
                                    'link' => Wa::panel()->listingURL($info),
//                                    'rows' => Wa::manager('site.section', $section)->getData()
                            ];
                        }
                    }
                }
            }
            $sections = $data;
        }

        return $sections;
    }
}