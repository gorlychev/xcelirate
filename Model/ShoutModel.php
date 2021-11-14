<?php

require_once PROJECT_ROOT_PATH . "/Model/Database.php";

class ShoutModel extends Database {

    public function getQuote($author, $limit) {

        $prettyAuthor = $this->prettyAuthor($author);
        return $this->select("SELECT * FROM quotes WHERE author = '" . $prettyAuthor . "' ORDER BY id ASC LIMIT $limit", []);
        /* $result= $this->select("SELECT * FROM quotes WHERE author = ? ORDER BY id ASC LIMIT ?", ['si',$author,$limit]);

          return $result; */
    }

    private function prettyAuthor($authorName) {
        $author = null;
        $authorArray = explode("-", $authorName);
        foreach ($authorArray as $item) {
            $author .= ucfirst($item) . " ";
        }
        $author = trim($author);
        return $author;
    }

    public function cachedResults($author, $limit, $cache_file_name = NULL, $expires = NULL) {
        global $request_type, $purge_cache, $limit_reached;
        $request_limit = 5;
        if (!$cache_file_name)
            $cache_file = dirname(__FILE__) . '/../cache/api-cache.json';
        if (!$expires)
            $expires = time() - 2 * 60 * 60;
        $cache_file = dirname(__FILE__) . '/../cache/' . $cache_file_name . ".json";
        if (!file_exists($cache_file)) {
            $fh = fopen($cache_file, 'w') or die("Can't create file");
        }

        // Check that the file is older than the expire time and that it's not empty
        if (filectime($cache_file) < $expires || file_get_contents($cache_file) == '') {

            // File is too old, refresh cache
            $api_results = $this->getQuote($author, $limit);

            $json_results = json_encode($api_results);

            if ($api_results && $json_results) {
                file_put_contents($cache_file, $json_results);
            } else {
                unlink($cache_file);
            }
        } else {
            // Fetch cache
            $json_results = file_get_contents($cache_file);
            $request_type = 'JSON';
        }

        return json_decode($json_results, true);
    }

}
