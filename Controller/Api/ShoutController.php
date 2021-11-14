<?php

class ShoutController extends BaseController {

    /**
     * curl -s http://localhost/xcelirate/index.php/shout/steve-jobs?limit=1
     */
    public function listAction($authorName) {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"]; //

        if (strtoupper($requestMethod) == 'GET') {
            try {
                $shoutModel = new ShoutModel();

                if (isset($_GET['limit']) && $_GET['limit']) {
                    $intLimit = $_GET['limit'];
                } else {
                    $intLimit = MAX_RATE;
                }
                if ($intLimit <= MAX_RATE) {
                    $cacheFile = $authorName . $intLimit;
                    $arrQuotes = $shoutModel->cachedResults($authorName, $intLimit, $cacheFile, 60);
                } else {
                    $arrQuotes[0]["quote"] = "Too large limit";
                }


                $out = array();
                if (empty($arrQuotes)) {
                    $out[] = "Empty result";
                } else {
                    foreach ($arrQuotes as $i => $item) {
                        $out[] = strtoupper($item["quote"]);
                    }
                }

                $responseData = json_encode($out);
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage() . 'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        // send output
        if (!$strErrorDesc) {
            $this->sendOutput(
                    $responseData,
                    array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)),
                    array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

}
