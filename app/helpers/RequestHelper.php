<?php

namespace Newsapp\Helpers;

class RequestHelper
{
    public static function addToQueryString(array $params) : string
    {
        $query = $_GET;
        foreach ($params as $k => $v) {
            $query[$k] = $v;
        }
        $url = '';

        foreach ($query as $k => $v) {
            $url .= "{$k}={$v}&";
        }
        return WEB_URL . '?' . trim($url, '&');
    }

    public static function removeFromQueryString(array $keys) : string
    {
        $query = $_GET;
        foreach ($keys as $k) {
            if (array_key_exists($k, $query)) {
                unset($query[$k]);
            }
        }

        $url = '';
        foreach ($query as $k => $v) {
            $url .= "{$k}={$v}&";
        }
        return WEB_URL . '?' . trim($url, '&');
    }
}
