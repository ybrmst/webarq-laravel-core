<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/23/2017
 * Time: 1:12 PM
 */

namespace Webarq\Manager\Cms\Query;


use Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Wa;
use Webarq\Manager\Cms\HTML\Form\AbstractInput;
use Webarq\Manager\Cms\HTML\Form\Input\FileInputManager;

class PostManager
{
    /**
     * Form type, create or edit
     *
     * @var
     */
    protected $type;

    /**
     * Pre-defined post data
     *
     * @var array
     */
    protected $post = [];

    /**
     * @var array
     */
    protected $files = [];

    /**
     * @param $type
     * @param array $post
     * @param array $inputs
     */
    public function __construct($type, array $post, array $inputs)
    {
        $this->type = $type;
        $this->post = $post;

// Pull out translation inputs
        $translation = array_pull($inputs, 'multilingual', []);

        if ([] !== $inputs) {
            foreach ($inputs as $input) {

                if (isset($this->post[$input->{'table'}->getName()][$input->{'column'}->getName()])) {
                    continue;
                }

                $value = $this->getValue($input);
// Value not set and should be ignored
                if ($this->isIgnored($input, $value)) {
                    continue;
                }
                if (is_array($value)) {
                    $this->multiRow($value, $input->{'table'}->getName(), $input->{'column'}->getName());
                } else {
                    $this->post[$input->{'table'}->getName()][$input->{'column'}->getName()] = $value;
                }
            }

            $this->translationRow($translation);
        }
    }

    /**
     * @param AbstractInput $input
     * @return mixed
     */
    protected function getValue(AbstractInput $input)
    {
        if (($input->isPermissible())) {
            if ($input instanceof FileInputManager) {
                return $this->inputFile($input);
            } else {
                $value = $input->getValue();
            }
        } else {
            $value = $input->isIgnored() ? null : $input->getImpermissible();
        }

        if (!$input->isScriptAllowed()) {
            $value = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $value);
        }

        if (!$this->isIgnored($input, $value) && !is_null($input->{'modifier'})) {
            if (is_array($value)) {
                foreach ($value as &$str) {
                    $str = Wa::modifier($input->{'modifier'}, $str);
                }
            } else {
                $value = Wa::modifier($input->{'modifier'}, $value);
            }
        }

        return $value;
    }

    /**
     * @param FileInputManager $input
     * @return mixed
     * @todo Testing array file input
     */
    protected function inputFile(FileInputManager $input)
    {
// File options
        $options = (array)$input->{'file'};
// Post File (s)
        $file = array_get($this->post, $input->getInputName(), Request::file($input->getInputName()));
        if (is_array($file)) {
            $result = [];
            foreach ($file as $key => $item) {
// Init uploader
                $uploader = $this->loadUploader(
                        array_get($options, 'type', 'file'),
                        $item,
                        array_get($options, 'upload-dir', '/'),
                        array_get($options, 'file-name'),
                        array_get($options, 'resize', [])
                );
// Push in to files
                $this->files[] = $uploader;
// Push in to post
                $result[] = $uploader->getPathName();
            }
        } else {
            if (null === $file) {
                return $input->getDefault();
            }

// Init uploader
            $uploader = $this->loadUploader(
                    array_get($options, 'type', 'file'),
                    $file,
                    array_get($options, 'upload-dir', '/'),
                    array_get($options, 'file-name'),
                    array_get($options, 'resize', [])
            );
// Push in to files
            $this->files[$input->getInputName()] = $uploader;

            $result = $uploader->getPathName();
        }

        return $result;
    }

    /**
     * @param string $type
     * @param UploadedFile $file
     * @param string $dir
     * @param mixed $name
     * @param array $resize
     */
    protected function loadUploader($type, UploadedFile $file, $dir, $name = null, array $resize = [])
    {
        $manager = Wa::manager('uploader.' . $type . ' uploader', $file, $dir, $name)
                ?: Wa::manager('uploader. file uploader', $file, $dir, $name);

        if ([] !== $resize) {
            $manager->setResize($resize);
        }

        return $manager;
    }

    /**
     * Check if given input is ignored or not
     *
     * @param AbstractInput $input
     * @param $value
     * @return bool
     */
    protected function isIgnored(AbstractInput $input, $value)
    {
        return Wa::getGhost() === $value
        || ($this->isEmpty($value) && $input->isIgnored())
        || $input->attribute()->has('readonly');
    }

    /**
     * Check if given value is empty or not
     *
     * @param $value
     * @return bool
     */
    protected function isEmpty($value)
    {
        return !is_object($value) && (is_array($value) ? [] === $value : ('' === trim($value) || null === $value));
    }

    /**
     * @param array $value
     * @param $table
     * @param $column
     */
    protected function multiRow(array $value, $table, $column)
    {
        foreach ($value as $key => $str) {

            if (isset($this->post[$table][$key][$column])) {
                continue;
            }

// @todo check translation for array row
            if (is_array($str)) {
                $str = base64_encode(serialize($str));
            }

            $this->post[$table][$key][$column] = $str;
        }
    }

    /**
     * Collect translation data
     *
     * @param array $inputs
     */
    protected function translationRow(array $inputs)
    {
        if ([] !== $inputs) {
            foreach ($inputs as $inputs) {
                foreach ($inputs as $code => $input) {
                    $t = \Wl::translateTableName($input->{'table'}->getName());
                    $value = $this->getValue($input);
                    if ($this->isIgnored($input, $value)) {
                        continue;
                    }
                    $this->post['translation'][$t][$code][$input->{'column'}->getName()] = $value;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }
}