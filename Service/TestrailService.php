<?php

namespace Test\TestrailBundle\Service;

use function curl_error;
use function curl_init;
use function curl_setopt;
use const CURLOPT_HTTPHEADER;
use function dump;
use function json_decode;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class TestrailService
{
    private $cacheApp;

    /**
     * TestrailService constructor.
     * @param $cacheApp
     */
    public function __construct($cacheApp)
    {
        $this->cacheApp = $cacheApp;
    }

    public function getData()
    {
        $curl = curl_init();

        $headers = [
            'Content-Type:application/json',
            'Authorization: Basic Y29kZWNyZXdfcHJvZEBib29zdGEuY286YzBkZUNyZXc='
        ];

        if ($url = 'https://boosta.testrail.io/index.php?/api/v2/get_runs/7'){
            curl_setopt($curl, CURLOPT_URL, $url);
        }

        if ($url = 'https://boosta.testrail.io/index.php?/api/v2/get_runs/6'){
            curl_setopt($curl, CURLOPT_URL, $url);
        }


        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        if ($result === false) {
            echo '<strong>Ошибка curl: </strong>' . curl_error($curl);
        }

        curl_close($curl);

        return $result;
    }

    public function getResponseData()
    {

        $this->cacheApp = new FilesystemAdapter('project', 3600);

        $key = 'project_id_6';

        $cachedItem = $this->cacheApp->getItem($key);

        if (!$cachedItem->isHit()) {
            $cachedItem->set(json_decode($this->getData()));
            $this->cacheApp->save($cachedItem);
        }

        $cachedItem = $cachedItem->get();

        $this->cacheApp->deleteItem($key);

        $failedRunList = [];

        foreach ($cachedItem as $item) {
            $failedCount = $item->failed_count;

            if ($failedCount > 0) {
                $failedRunList[] = $item;
                break;
            }
        }

        return $failedRunList;
    }
}