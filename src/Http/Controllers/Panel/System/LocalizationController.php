<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 3/23/2017
 * Time: 10:50 AM
 */

namespace Webarq\Http\Controllers\Panel\System;


use Request;
use URL;
use Wa;
use Webarq\Http\Controllers\Panel\BaseController;
use Webarq\Manager\Cms\HTML\FormConfigManager;
use Webarq\Manager\HTML\Table\BodyManager;

class LocalizationController extends BaseController
{
    /**
     * @var string
     */
    protected $layout = 'localization';

    /**
     * Localization file name
     *
     * @var string
     */
    protected $file;

    /**
     * @var array
     */
    protected $files = [];

    /**
     * @var
     */
    protected $locale = 'en';

    public function actionGetIndex()
    {
        $rows = trans($this->file, [], 'messages', $this->locale);

        if (is_array($rows)) {
// Load HTML table manager
            $manager = Wa::html('table');
// Set table title
            $manager->setTitle(
                    title_case($this->file . ' localization'),
                    ':webarq::themes.admin-lte.section.localization',
                    [
                            'locale' => $this->locale,
                            'files' => $this->files
                    ]
            );
// Add head tag
            $manager->addHead()->addRow()
                    ->addCell('Key', null)
                    ->addCell('Translation', null)
                    ->addCell('Action', null);
// Add body tag
            $body = $manager->addBody();
// Add row in to body
            $this->makeRow($body, $rows);
// Set content
            $this->layout->{'rightSection'} = $manager->toHtml();
// Pass another value to layout
            $this->layout->with([
                    'file' => $this->file
            ]);
        }
    }

    protected function makeRow(BodyManager $body, array $rows, $parent = '')
    {
        foreach ($rows as $key => $value) {
            if (is_array($value)) {
                $body->addRow()->addCell(title_case(str_replace(['-', '_'], ' ', $key)), [
                        'colspan' => 3,
                        'style' => 'text-align:center; background-color: #cccccc'
                ]);
                $this->makeRow($body, $value, $key);
            } else {
                $url = trim($parent . '.' . $key, '.');
                $bs = base64_encode($url);
                $opt = [];

                if ($bs === request()->query('h')) {
                    $opt += [
                        'style' => 'background-color: #333;color: #fff;',
                    ];
                }

                $url = URL::panel('system/localization/edit/' . $this->locale . '/' . $this->file . '/' . $bs);
                $body->addRow($opt)
                        ->addCell($key)
                        ->addCell($value)
                        ->addCell('<a href="' . $url . '" alt="edit" class="fa fa-edit"> Edit</a>', ['id' => $bs]);
            }
        }
    }

    /**
     * @return mixed
     */
    public function actionGetEdit()
    {
        if (null !== ($key = $this->getParam(3))
                && false !== ($key = base64_decode($key))
        ) {
// Load form
            $form = $this->loadForm();
// Add input
            $this->addInput($form, $key);
// Set content
            $this->layout->{'rightSection'} = $form->compile()->toHtml();
        } else {
            return $this->actionGetForbidden();
        }
    }

    /**
     * @return object Webarq\Manager\HTML\FormConfigManager
     */
    protected function loadForm()
    {
        return Wa::manager('cms.HTML!.form config', $this->admin, [
                'module' => $this->getModule(),
                'panel' => $this->getPanel(),
                'title' => 'Localization Edit > ' . ucfirst($this->file)
        ]);
    }

    /**
     * @param FormConfigManager $form
     * @param $key
     * @param mixed $value
     */
    protected function addInput(FormConfigManager $form, $key, $value = null)
    {
        if (null === $value) {
            $value = trans($this->file . '.' . $key, [], 'messages', $this->locale);
        }

        $form->addInput('textarea', $key, $value, [
                'name' => $this->file . '[' . $key . ']',
                'title' => title_case(str_replace('.', ' > ', str_replace(['-', '_'], ' ', $key)))
        ]);
    }

    /**
     * @return mixed
     */
    public function actionPostEdit()
    {
        if (null !== ($key = $this->getParam(3))
                && false !== ($key = base64_decode($key))
        ) {
            $collections = trans($this->file, [], 'messages', $this->locale);


            $input = Request::input();
            unset($input['_token']);
// Load form
            $form = $this->loadForm();
            if ([] !== $input) {
                $error = false;

                foreach ($input as $file => $groups) {
                    foreach ($groups as $name => $value) {
                        if ('' === trim($value)) {
                            $form->setAlert(['Translation should not be empty'], 'warning');

                            $this->addInput($form, $key, $value);

                            $error = true;
                        } else {
                            array_set($collections, $key, $value);
                        }
                    }
                }

                if (!$error) {
                    $path = app_path() . DIRECTORY_SEPARATOR . '..'
                            . DIRECTORY_SEPARATOR . 'resources'
                            . DIRECTORY_SEPARATOR . 'lang'
                            . DIRECTORY_SEPARATOR . $this->locale
                            . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $this->file) . '.php';


// Record to file
                    $f = fopen($path, 'w+');

                    fwrite($f, '<?php return '
                            . PHP_EOL
                            . '       '
                            . $this->var_export_array($collections, '       ') . ';');
                    fclose($f);

                    $this->setTransactionMessage(Wa::trans('webarq::core.messages.success-update'), 'success');

                    return redirect()->to(URL::panel('system/localization?h=' . $this->getParam(3) . '#' . $this->getParam(3)));
                }
            } else {
                return $this->actionGetForbidden();
            }

            $this->layout->{'rightSection'} = $form->compile()->toHtml();
        } else {
            return $this->actionGetForbidden();
        }
    }

    protected function var_export_array($var, $indent = "")
    {
        switch (gettype($var)) {
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                            . ($indexed ? "" : $this->var_export_array($key) . " => ")
                            . $this->var_export_array($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            default:
                return var_export($var, TRUE);
        }
    }

    public function before()
    {
        $this->locale = $this->getParam(1, $this->locale);

        $file = $this->panel->getAttribute('group');
        if (!is_null($file)) {
            if (!is_array($file)) {
                $file = explode(',', $file);
            }

            $this->file = $this->getParam(2, current($file));

            $this->files = $file;
        }

        return parent::before();
    }

    public function after()
    {
// While on form hide search box
        view()->share('shareSearchBox', false);

        return parent::after();
    }

    protected function isAccessible()
    {
        return true;
    }
}