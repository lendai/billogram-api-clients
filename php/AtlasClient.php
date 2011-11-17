<?php

class AtlasClient
{
    /**
      * @description PHP class for Atlas Express REST API
      * @author Carl Oscar Aaro at Agigen http://agigen.se/
      */

    private static $_apiId = "4e8eddb37cff852384e8eddb37d019"; /* Set the apiId to your Atlas Express API ID */
    private static $_apiPassword = "myPassword"; /* Set the apiPassword to your Atlas Express API password */
    private static $_apiHost = "http://api.atlasexpress.se"; /* Set to https://sandbox.atlasexpress.se for testing */

    private static function _request($url, $request = "GET", $params = null)
    {
        mb_internal_encoding("UTF-8");

        $actualUrl = $url = self::$_apiHost . (strpos($url, '/') !== 0 ? "/" : "") . $url;

        $streamParams = array('http' => array(
            'method' => $request,
            'ignore_errors' => true,
            'header' => "Authorization: Basic " . base64_encode(self::$_apiId . ":" . self::$_apiPassword) . "\r\n"
        ));

        if ($params !== null) {
            $params = http_build_query($params);
            if ($request == "POST")
                $streamParams['http']['content'] = $params;
            else
                $url .= "?" . $params;
        }

        $context = stream_context_create($streamParams);
        $fp = fopen($url, "rb", false, $context);
        if (!$fp)
            $result = false;
        else
            $result = stream_get_contents($fp);

        if ($result === false)
            throw new Exception("{$request} request to {$url} failed");

        if (strpos($actualUrl, ".pdf") !== false && !@json_decode($result)) {
            header("Content-type: application/pdf");
            header("Content-length: " . strlen($result));
            echo $result;
            die;
        }

        $return = json_decode($result);
        if ($return === null) {
            $metaData = stream_get_meta_data($fp);
            foreach ($metaData['wrapper_data'] as $line) {
                if (preg_match('/^HTTP\/1\.[01] (\d{3})\s*(.*)/', $line, $match)) {
                    $statusCode = $match[1];
                    $status = $match[2];
                    if ($statusCode == 401)
                        throw new Exception("Invalid login, request returned {$statusCode} {$status}");
                    else if ($statusCode != 200)
                        throw new Exception("{$request} request to {$url} returned {$statusCode} {$status}");
                    else
                        break;
                }
            }
            throw new Exception("Result is not JSON");
        }
        return $return;
    }

    public static function getInvoice($invoiceNumber)
    {
        return self::_request("/invoices/{$invoiceNumber}", "GET");
    }

    public static function getInvoiceByOcr($ocr)
    {
        return self::_request("/invoices", "GET", array('ocr' => $ocr, 'key' => 'ocr'));
    }

    public static function getInvoiceByUniqueId($uniqueId)
    {
        return self::_request("/invoices", "GET", array('unique_id' => $uniqueId, 'key' => 'unique_id'));
    }

    public static function showInvoicePdf($invoiceNumber)
    {
        self::_request("/invoices/{$invoiceNumber}.pdf", "GET");
    }

    public static function showInvoicePdfByOcr($ocr)
    {
        self::_request("/invoices.pdf", "GET", array('ocr' => $ocr, 'key' => 'ocr'));
    }

    public static function showInvoicePdfByUniqueId($uniqueId)
    {
        self::_request("/invoices.pdf", "GET", array('unique_id' => $uniqueId, 'key' => 'unique_id'));
    }

    public static function createInvoice($data)
    {
        return self::_request("/invoices", "POST", $data);
    }

    public static function updateInvoice($invoiceNumber, $data)
    {
        return self::_request("/invoices/{$invoiceNumber}", "POST", $data);
    }

    public static function sendInvoice($invoiceNumber)
    {
        return self::_request("/invoices/{$invoiceNumber}", "POST", array('event' => 'Send'));
    }

    public static function sellInvoice($invoiceNumber)
    {
        return self::_request("/invoices/{$invoiceNumber}", "POST", array('event' => 'Sell'));
    }

    public static function deleteInvoice($invoiceNumber)
    {
        return self::_request("/invoices/{$invoiceNumber}", "DELETE");
    }

    public static function verifyCallback($post)
    {
        if (!isset($post) || !is_array($post)) return false;

        $nonce = isset($post['nonce']) ? $post['nonce'] : '';
        $hash = isset($post['hash']) ? $post['hash'] : '';

        if (!$hash || !$nonce || !self::$_apiId) return false;
        if ($hash != md5($nonce . self::$_apiId)) return false;

        return true;
    }
}
