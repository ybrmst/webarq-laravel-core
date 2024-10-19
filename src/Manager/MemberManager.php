<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/15/2016
 * Time: 5:09 PM
 */

namespace Webarq\Manager;


class MemberManager extends WatchdogAbstractManager
{
    /**
     * Identify admin
     * @param array|number $credentials
     * @return $this
     */
    public function identify($credentials = [])
    {
        return $this;
    }

    protected function setProfile(array $data)
    {
        $this->profile = $data;
    }
}