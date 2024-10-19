<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 6:22 PM
 */

namespace Webarq\Http\Controllers\Site;


use Wa;
use Webarq\Http\Controllers\Webarq;
use Webarq\Manager\Site\MenuManager;


class BaseController extends Webarq
{
    protected $layout = 'base';

    /**
     * @var object Webarq\Manager\Site\MenuManager
     */
    protected $menu;

    /**
     * @var
     */
    protected $metaTitle;

    /**
     * @var
     */
    protected $metaDescription;

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->themes = config('webarq.system.site.themes', 'front-end');

        view()->share('shareThemes', $this->themes);

        parent::__construct($params);

        $this->menu = $this->bindMenu(array_pull($params, 'menu'));
    }

    protected function bindMenu(MenuManager $menu = null)
    {
        return $menu;
    }

    /**
     * Escaping function. This method execute through routing file
     *
     * @return mixed
     */
    public function escape()
    {
        if (!$this->menu instanceof MenuManager || null === $this->menu->getActive()) {
            return $this->actionGetForbidden();
        } elseif ('0' === Wa::config('system.site.online')) {
            return Wa::getThemesView('front-end', 'layout.offline');
        }

// Auto template is on
        if (true === config('webarq.system.site.auto-template')) {
            if ([] !== $this->menu->getActive() &&
                    view()->exists('webarq::themes.front-end.layout.' . $this->menu->getActive()->template)
            ) {
                $this->setLayout($this->menu->getActive()->template);
            }
        }
    }

    /**
     * @param string $name Layout name (no need to write down the full path)
     * @param array $attributes
     * @return mixed
     */
    protected function setLayout($name, array $attributes = [])
    {
        parent::setLayout($name);

// Default layout meta
        $this->layout->with($attributes + [
                        'metaTitle' => Wa::config('system.site.meta.title'),
                        'metaDescription' => Wa::config('system.site.meta.description')
                ]);
    }

    /**
     * Basic index action
     */
    public function actionGetIndex()
    {
        $this->layout->{'shareSections'} = $this->listSectionManager();
    }

    /**
     * @param array $params Section view params
     *        'section-key-1' => array params
     *        'section-key-2' => array params
     * @return array
     */
    protected function listSectionManager(array $params = [])
    {
        $managers = [];

        if ([] !== ($sections = $this->getActiveSection())) {
            foreach ($sections as $section) {
                $key = array_get($section, 'key', 'Unknown');
// Overwrite section id
                $section['id'] = $this->menu->getActive()->id . '.' . $section['id'];
// Get section view params
                $options = (array)array_pull($params, $key, [])
                        + config('webarq.template.sections.' . $key, [])
                        + ['template' => $this->menu->getActive()->template];

                $managers[] = Wa::manager('site.section', $section + $options);
            }
        }

        return $managers;
    }

    /**
     * @return array|mixed
     */
    protected function getActiveSection()
    {
        return [] === $this->menu->getActive()
                ? $this->menu->getActive()
                : Wa::model('section')->getTemplateSection($this->menu->getActive()->template);
    }

    /**
     * After method
     *
     * Calling from route file as final step if main method does not return anything
     *
     * @return object
     */
    public function after()
    {
// Overwrite meta title n description with active menu meta
        if (!empty($this->menu->getActive()) && !isset($this->metaTitle)) {
            $this->metaTitle = $this->menu->getActive()->meta_title;
            $this->metaDescription = $this->menu->getActive()->meta_description;
        }

        if (isset($this->metaTitle)) {
            $this->layout->with(['metaTitle' => $this->metaTitle]);
        }

        if (isset($this->metaDescription)) {
            $this->layout->with(['metaDescription' => $this->metaDescription]);
        }


        return parent::after();
    }

    protected function setMetaTitle($meta)
    {
        $this->setMeta($meta, 'title');
    }

    protected function setMeta($meta, $key)
    {
        if ($meta) {
            $this->{'meta' . ucfirst($key)} = $meta;
        }
    }

    protected function setMetaDescription($meta)
    {
        $this->setMeta($meta, 'description');
    }
}