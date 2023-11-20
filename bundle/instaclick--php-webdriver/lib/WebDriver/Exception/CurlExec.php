<?php

/**
 * @copyright 2013 Anthon Pang
 * @license Apache-2.0
 *
 * @package WebDriver
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */

namespace WebDriver\Exception;

use WebDriver\Exception as BaseException;

/**
 * WebDriver\Exception\CurlExec class
 *
 * @package WebDriver
 */
final class CurlExec extends BaseException
{
    /**
     * @var array
     */
    private $curlInfo = array();

    /**
     * Get curl info
     *
     * @return array
     */
    public function getCurlInfo()
    {
        return $this->curlInfo;
    }

    /**
     * Set curl info
     *
     * @param array $curlInfo
     */
    public function setCurlInfo($curlInfo)
    {
        $this->curlInfo = $curlInfo;
    }
}
