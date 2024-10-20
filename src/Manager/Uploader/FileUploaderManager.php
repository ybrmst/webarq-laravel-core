<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/23/2017
 * Time: 3:07 PM
 */

namespace Webarq\Manager\Uploader;


use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Str;

class FileUploaderManager
{
    /**
     * @var UploadedFile
     */
    protected $file;

    /**
     * @var string
     */
    protected $dir;

    /**
     * File name
     *
     * @var
     */
    protected $name;

    /**
     * Full path file name
     *
     * @var
     */
    protected $fullPath;

    public function __construct(UploadedFile $file, $dir, $name = null)
    {
        $this->file = $file;
        $this->dir = $this->createDir($dir);
        $this->setName($name);
    }

    protected function createDir($path)
    {
        if ('/' !== $path) {
            $dirs = explode('/', strtolower($path));
            $path = '';
            foreach ($dirs as $dir) {
                if ('' === $dir) {
                    continue;
                }

                $path = trim($path . '/' . $dir, '/');

                if (!is_dir($path)) {
                    mkdir($path, 0755);
                }
            }
        }

        return $path ?: '/';
    }

    public function upload()
    {
        return $this->file->move(public_path($this->dir), $this->getName());
    }

    /**
     * @return mixed
     */
    public function getName()
    {
// Make sure file name has it's extension
        $ext = $this->file->getClientOriginalExtension();
        if (!Str::endsWith($this->name, $ext)) {
            return $this->name . '.' . $ext;
        }

        return $this->name;
    }

    /**
     * Set file name
     *
     * @param null $name
     * @param bool $uniqueId
     */
    public function setName($name = null, $uniqueId = true)
    {
        if (null === $name) {
            $name = $this->file->getClientOriginalName();
            $name = substr($name, 0, strrpos($name, '.'));

            if (true === $uniqueId) {
                $name = uniqid() . '-' . $name;
            }
        }

        $name = str_slug(strtolower($name));

        $this->name = $name;
    }

    public function getPathName()
    {
        if (null === $this->fullPath) {
            if ('/' !== $this->dir) {
                $this->fullPath = trim($this->dir, '/') . '/';
            }

            $this->fullPath .= $this->getName();
        }

        return $this->fullPath;
    }
}