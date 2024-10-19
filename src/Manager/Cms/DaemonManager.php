<?php
/**
 * Created by PhpStorm
 * Date: 18/02/2017
 * Time: 16:59
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Manager\Cms;


use Hash;
use Wa;

class DaemonManager
{
    protected $items;

    public function __construct($credentials = [])
    {
        if (null !== ($model = Wa::model('configuration'))) {
            if (is_array($credentials)) {
                $get = $model->select('setting')
                        ->whereKey(hash('sha1', array_get($credentials, 'username', 'who are you?')))
                        ->first();
            } else {
                $get = $model->select('setting')->whereKey($credentials)
                        ->first();
            }

            if (null !== ($get)) {
                try {
                    $this->items = unserialize(base64_decode(decrypt($get->setting)));
                } catch (\Exception $e) {

                }
                if (is_array($this->items)) {
                    $this->items['password'] = Hash::make(array_pull($this->items, 'secret', 'who are you?'));
                    $this->items['username'] = array_pull($this->items, 'name', 'who are you?');
                    $this->items['id'] = hash('sha1', array_get($credentials, 'username', 'who are you?'));
                }
            }
        }
    }

    public function getItems()
    {
        return $this->items;
    }
}