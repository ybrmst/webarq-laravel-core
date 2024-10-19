<?php
/**
 * Created by PhpStorm
 * Date: 05/02/2017
 * Time: 12:26
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Manager\Cms\HTML;


use Wa;
use Webarq\Manager\Cms\HTML\Form\AbstractInput;

class FormConfigManager extends FormManager
{
    /**
     * @param $type
     * @param $name
     * @param null $value
     * @param array $attr
     */
    public function addInput($type, $name, $value = null, array $attr = [])
    {
        $attr['type'] = $type;
        $attr['value'] = $value;

        $input = $this->makeInput($name, $attr);

        $this->inputs[$input->getInputName()] = $input;
    }

    /**
     * @param $name
     * @param array $attr
     * @return mixed
     */
    protected function makeInput($name, array $attr)
    {
        $mod = Wa::module($this->module);
        $pnl = $mod->getPanel($this->panel);
// Set attribute name
        if (!isset($attr['name'])) $attr['name'] = $name;
// Input type
        $type = isset($attr['file']) ? 'file' : array_get($attr, 'type', 'null');
// This is could be pain on the process, but due to laravel input form method behaviour is different
// one from another, we need class helper to enable us adding consistent parameter
        $input = Wa::load('manager.cms.HTML!.form.input.' . $type . ' input', $mod, $pnl, $attr)
                ?: Wa::load('manager.cms.HTML!.form.input.default input', $mod, $pnl, $attr);

        return $this->inputManagerDependencies($input);
    }

    /**
     * @param array $inputs
     */
    protected function prepareInputs(array $inputs)
    {
        if ([] !== $inputs) {
            $master = null;
            foreach ($inputs as $name => $attr) {
                if (!is_array($attr)) continue;
// Build input
                $input = $this->makeInput($name, $attr);
// Process valid input
                if ($input instanceof AbstractInput && $input->isValid() && $input->isPermissible()) {
                    $this->collectInput($input);
                }
            }
        }
    }

    protected function collectInput(AbstractInput $input)
    {
        if ($input->isMultilingual()) {
            foreach (\Wl::getCodes() as $code) {
                $clone = clone $input;
                $clone->attribute()->setName($clone->name, $code);
                $clone->setTitle($clone->getTitle() . ' (' . strtoupper($code) . ')');
                $this->inputs[$clone->getInputName()] = $clone;
            }
        } else {
            $this->inputs[$input->getInputName()] = $input;
        }
    }
}