<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/6/2017
 * Time: 4:16 PM
 */

namespace Webarq\Manager\HTML\Table\Driver;


abstract class DriverAbstractManager
{
    /**
     * There is two ways to provided data value:
     *
     * $data = [['id' => 1, 'name' => 'John Doe'], ['id' => 2, 'name' => 'Jane Doe'], ['name' => '??', 'id' => '??']]
     *
     * or
     *
     * $data = [
     *      'head' => ['id', 'name' => [some html attributes]],
     *      'rows' => [
     *          [1, 'John Doe']
     *          [2, 'Jane Doe']
     *          ['??' , '??']
     *      ]
     *
     * ]
     *
     * @var array
     */
    protected $data = [];

    /**
     * @param mixed $key
     * @return array
     */
    public function getData($key = null)
    {
        if (!isset($this->data['rows'])
                && [] === $this->data
                && null !== ($rows = $this->getRows())
                && is_array($rows) && [] !== $rows
        ) {
            $this->data['rows'] = [];
            foreach ($rows as $iteration => $row) {
                foreach ($row as $column => $value) {
                    if (!isset($this->data['head'][$column])) {
                        $this->data['head'][$column] = $column;
                    }
                    $this->data['rows'][$iteration][$column] = $value;
                }
            }
        }

        return array_get($this->data, $key, []);
    }

    /**
     * Get data rows
     *
     * @return mixed
     */
    abstract protected function getRows();

    /**
     * Provide sampling data
     *
     * @return mixed
     */


    /**
     * @inheritDoc
     */
    public function sampling()
    {
        $this->data = [
                'head' => ['No', 'Name' => ['style' => 'background-color:#333'], 'Email', 'Status'],
                'rows' => [
                        [1, 'John Doe', 'john.doe@mail.dev', 'Father'],
                        [2, 'Jane Doe', 'sarah.doe@mail.dev', 'Mother'],
                        [3, 'Janie Doe', 'janie.doe@mail.dev', 'Daughter'],
                        [4, 'Richard Miles', 'miles.richard@mail.dev', 'Cousin']
                ]
        ];
    }
}