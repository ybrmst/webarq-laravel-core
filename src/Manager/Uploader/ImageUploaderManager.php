<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/23/2017
 * Time: 3:09 PM
 */

namespace Webarq\Manager\Uploader;


use Image;

class ImageUploaderManager extends FileUploaderManager
{
    protected $width;

    protected $height;

    public function upload()
    {
        $move = parent::upload();

        if (null !== $this->width || null !== $this->height) {
            $this->resizing($move->getRealPath(), $this->width, $this->height);
        }

        return $move;
    }

    protected function resizing($source, $width, $height, $scale = true)
    {
        $img = Image::make($source);

        if (($img->width() !== $width && null !== $width) || ($img->height() !== $height && isset($height))) {
            if ($height && $width) {
                if ($scale) {
                    if ($img->height() > $img->width()) {
                        if ($img->height() > $height) {
                            $img->resize(null, $height, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        }
                        if ($img->width() > $width) {
                            $img->resize($width, null, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        }
                    } else {
                        if ($img->width() > $width) {
                            $img->resize($width, null, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        }
                        if ($img->height() > $height) {
                            $img->resize(null, $height, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        }
                    }

                    $canvas = Image::canvas($width, $height);

                    $canvas->insert($img, 'center');
                    return $canvas->save($source);
                } else {
                    $img->resize($width, $height);
                }
            } else {
                if (!$width) {
                    $img->resize(null, $height, function($constraint) {
                        $constraint->aspectRatio();
                    });
                }

                if (!$height) {
                    $img->resize($width, null, function($constraint) {
                        $constraint->aspectRatio();
                    });
                }
            }

            return $img->save();
        }
    }

    public function setResize(array $options)
    {
        $this->width = array_get($options, 'width');
        $this->height = array_get($options, 'height');
    }
}