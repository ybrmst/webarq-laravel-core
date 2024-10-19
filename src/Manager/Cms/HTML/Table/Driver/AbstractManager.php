<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/31/2017
 * Time: 12:00 PM
 */

namespace Webarq\Manager\Cms\HTML\Table\Driver;


abstract class AbstractManager
{
    /**
     * Get rows
     *
     * @return array
     */
    abstract public function getRows();

    /**
     * Sampling data
     *
     * @return array
     */
    public function sampling()
    {
        return [
                'head' => ['No', 'Name' => ['style' => 'background-color:#333'], 'Email', 'Status'],
                'rows' => [
                        [1, 'John Doe', 'john.doe@mail.dev', 'Father'],
                        [2, 'Jane Doe', 'sarah.doe@mail.dev', 'Mother'],
                        [3, 'Janie Doe', 'janie.doe@mail.dev', 'Daughter'],
                        [4, 'Richard Miles', 'miles.richard@mail.dev', 'Cousin']
                ]
        ];
    }

    /**
     * Driver paginator
     *
     * @param int $limit
     * @return mixed
     */
    abstract public function paginate($limit = 10);
}