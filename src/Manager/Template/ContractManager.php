<?php
/**
 * Created by PhpStorm
 * Date: 15/02/2017
 * Time: 22:40
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace WEBARQ\Manager\Template;


interface ContractManager
{
    public function preview(array $post);

    public function render(array $post);
}