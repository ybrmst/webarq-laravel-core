<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/6/2017
 * Time: 3:17 PM
 */

namespace Webarq\Manager\HTML\Table\Driver;


use URL;

class JsonManager extends DriverAbstractManager
{
    /**
     * @var
     */
    protected $string;

    /**
     * @param null $string
     */
    public function __construct($string = null)
    {
        if (is_callable($string)) {
            $string($this);
        } elseif (URL::isValidUrl($string)) {
            $this->setURL($string);
        } else {
            $this->setString($string);
        }
    }

    /**
     * Set url
     *
     * @param $string
     * @return $this
     */
    public function setURL($string)
    {
// Make sure our server enabled to access external url
        ini_set("allow_url_fopen", 1);

        $this->setString(file_get_contents($string));

        return $this;
    }

    /**
     * @param $string
     */
    public function setString($string)
    {
        $this->string = $string;
    }

    /**
     * @inheritDoc
     */
    public function sampling()
    {
        $this->setString(json_encode([
                ['id' => 1, 'name' => 'John Doe'],
                ['id' => 2, 'name' => 'Sarah Doe'],
                ['name' => '??', 'id' => 3]
        ]));
    }

    /**
     * @inheritDoc
     */
    protected function getRows()
    {
        if (null !== $this->string) {
            try {
                $decode = json_decode($this->string, true);
                if (false !== $decode) {
                    return $decode;
                }
            } catch (\Exception $e) {
                return null;
            }
        }
    }
}