<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/16/2017
 * Time: 7:20 PM
 */

namespace Webarq\Laravel;


class FormBuilder extends \Collective\Html\FormBuilder
{
    /**
     * Create a select box field.
     *
     * @param  string $name
     * @param  array $list
     * @param  string $selected
     * @param  array $options
     * @param  array $optionAttributes
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function select($name, $list = [], $selected = null, $options = [], $optionAttributes = [])
    {
        // When building a select box the "value" attribute is really the selected one
        // so we will use that when checking the model or session for a value which
        // should provide a convenient method of re-populating the forms on post.
        $selected = $this->getValueAttribute($name, $selected);

        $options['id'] = $this->getIdAttribute($name, $options);

        if (!isset($options['name'])) {
            $options['name'] = $name;
        }

        // We will simply loop through the options and build an HTML value for each of
        // them until we have an array of HTML declarations. Then we will join them
        // all together into one single HTML element that can be put on the form.
        $html = [];

        if (isset($options['placeholder'])) {
            $html[] = $this->placeholderOption($options['placeholder'], $selected);
            unset($options['placeholder']);
        }

        foreach ($list as $value => $display) {
            $html[] = $this->getSelectOption($display, $value, $selected, (array)$optionAttributes);
        }

        // Once we have all of this HTML, we can join this into a single element after
        // formatting the attributes into an HTML "attributes" string, then we will
        // build out a final select statement, which will contain all the values.
        $options = $this->html->attributes($options);

        $list = implode('', $html);

        return $this->toHtmlString("<select{$options}>{$list}</select>");
    }

    /**
     * Get the select option for the given value.
     *
     * @param  string $display
     * @param  string $value
     * @param  string $selected
     * @param  array $attributes
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function getSelectOption($display, $value, $selected, array $attributes = [])
    {
        if (is_array($display)) {
            return $this->optionGroup($display, $value, $selected, $attributes);
        }

        return $this->option($display, $value, $selected, $attributes);
    }

    /**
     * Create an option group form element.
     *
     * @param  array $list
     * @param  string $label
     * @param  string $selected
     * @param  array $attributes
     *
     * @return \Illuminate\Support\HtmlString
     */
    protected function optionGroup($list, $label, $selected,  array $attributes = [])
    {
        $html = [];

        foreach ($list as $value => $display) {
            $html[] = $this->option($display, $value, $selected, $attributes);
        }

        return $this->toHtmlString('<optgroup label="' . e($label) . '">' . implode('', $html) . '</optgroup>');
    }

    /**
     * Create a select element option.
     *
     * @param  string $display
     * @param  string $value
     * @param  string $selected
     * @param  array $attributes
     *
     * @return \Illuminate\Support\HtmlString
     */
    protected function option($display, $value, $selected,  array $attributes = [])
    {
        $selected = $this->getSelectedValue($value, $selected);

        $options = ['value' => $value, 'selected' => $selected] + $attributes;

        return $this->toHtmlString('<option' . $this->html->attributes($options) . '>' . e($display) . '</option>');
    }
}