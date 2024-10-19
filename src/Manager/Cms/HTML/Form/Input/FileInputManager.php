<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/23/2017
 * Time: 9:12 AM
 */

namespace Webarq\Manager\Cms\HTML\Form\Input;


use HTML;
use Illuminate\Http\UploadedFile;
use URL;
use Wa;
use Webarq\Manager\Cms\HTML\Form\AbstractInput;

class FileInputManager extends AbstractInput
{
    /**
     * @var
     */
    protected $file;

    public function buildInput()
    {
        $attr = $this->attribute()->toArray();
        if (isset($this->file['mimes'])) {
            $attr['accept'] = '.' . implode(',.', (array)$this->file['mimes']);
        }
// Preview file
        $p = array_get($this->file, 'preview');
        if (true === $p || is_numeric($p)) {
            $attr['class'] = trim(array_get($attr, 'class', '') . ' previewLoader');
        }
// Remove form control class
        if (isset($attr['class'])) {
            $attr['class'] = trim(str_replace('form-control', '', $attr['class']));
        }


        $ipt = \Form::file($this->name, $attr);

//        Hidden input while not empty
        $ipt .= \Form::hidden('hidden_' . $this->name, $this->value);

        if (true === $p || is_numeric($p)) {
            $ipt .= $this->makePreviewImage($p);
        } elseif (is_file($this->value)) {
            $ipt .= '<br/><a href="' . asset($this->value) . '" target="_blank">' . $this->value . '</a>';
        }

        return $ipt;
    }

    /**
     * @param null $width
     * @return string
     */
    protected function makePreviewImage($width = null)
    {
        if (!is_numeric($width)) {
            $width = array_get($this->file, 'resize.width', 100);
        }

//        The image is a tmp file, use the previous one
        if ($this->value instanceof UploadedFile) {
            $src = $this->getRemoteValue();
        } else {
            $src = $this->value ?: 'vendor/webarq/default/img/boxed-bg.jpg';
        }

        return '<br/>' . HTML::image(URL::asset($src), null, [
                'style' => 'width:' . $width . 'px',
                'id' => $this->name . '-img-preview'
        ]);
    }

    public function __clone()
    {
        parent::__clone();

        if (null !== $this->file) {
            $opt = ['file' => $this->file];
            $this->setRule($opt);
        }
    }

}