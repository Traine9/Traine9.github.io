<?php

class Tool_Example {

    public function process() {
        $files = $this->getDirContents('/home/devel003/uewiki/Traine9.github.io/Unreal Engine Wiki/mediawikiv2-website-prod05.ol.epicgames.net');
        $pathArray = [];
        foreach ($files as $path) {
            if (!in_array(pathinfo($path, PATHINFO_EXTENSION), ['htm', 'html'])) {
                continue;
            }
            $content = file_get_contents($path);
            $result = preg_match('/<h1[^>]+id="firstHeading"[^>]+>(.*)<\/h1>/u', $content, $r);
            $title = @$r[1];
            $title = trim($title);
            $title = htmlspecialchars_decode($title);
            if (!$result || $title == 'We\'re working on a new Wiki!') {
                continue;
            }
            $pathArray[] = array(
                'path' => $path,
                'title' => $title
            );
        };
        //print_r($pathArray);

        //next cycle takes 2862 seconds on php7.4-cli and 4GHz core
        //php -f tools/1-parse.php | tee tools/resources/logs.php

        foreach ($files as $path) {
            if (!in_array(pathinfo($path, PATHINFO_EXTENSION), ['htm', 'html'])) {
                continue;
            }
            $result = ['path' => $path];
            $find = false;
            foreach ($pathArray as $pathTitle) {
                $content = file_get_contents($path);
                $title = $pathTitle['title'];

                $title = preg_replace('/\s/ius', '_', $title);

                $preg = '/<a[^>]+href="([^"]+\.php.title='.trim(preg_quote($title, '/'), '/').'[^"]*)"[^>]+>/u';
                $preg2 = '/<a[^>]+href="([^"]+\.php.title='.trim(preg_quote(str_replace('%3A', ':', rawurlencode($title)), '/'), '/').'[^"]*)"[^>]+>/u';

                $r1 = preg_match($preg, $content, $r);
                $r2 = preg_match($preg2, $content, $rr);
                if ($r1 || $r2) {
                    $find = true;
                    if ($r1) {
                        $this->addLog($r1);
                    }
                    if ($rr) {
                        $this->addLog($rr);
                    }
                    if ($rr && !$r1) {
                        $pathTitle['title'] = str_replace('%3A', ':', rawurlencode($title));
                    }
                    $result['title'][] = $pathTitle;
                }
            }
            $this->addLog($path);
            if ($find) {
                $resultArray[] = $result;
            }
        };
        $this->addLog(var_export($resultArray, true));
        $this->printLog();
    }

    private $_log = [];
    public function addLog($data) {
        if (!is_string($data)) {
            $data = print_r($data, true);
        }
        $this->_log[] = $data;
    }

    public function printLog() {
        print implode(PHP_EOL, $this->_log);
    }

    public function getDirContents($dir) {
        $path = array();
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($iterator as $file) {
            if ($file->isDir()) continue;
            $path[] = $file->getPathname();
        }
        return $path;
    }
}
$x = new Tool_Example();
$x->process();