<?php

class Tool_Example {

    public function process() {
        ModeService::Get()->verbose('Tool running in verbose mode!');
        $files = $this->getDirContents('/home/devel003/uewiki/Traine9.github.io/Unreal Engine Wiki/mediawikiv2-website-prod05.ol.epicgames.net');
        $pathArray = [];
        foreach ($files as $path) {
            if (!in_array(pathinfo($path, PATHINFO_EXTENSION), ['htm', 'html'])) {
                continue;
            }
            $content = file_get_contents($path);
            $result = preg_match('/<h1[^>]+id="firstHeading"[^>]+>(.*)<\/h1>/u', $content, $r);
            $title = @$r[1];
            if (!$result || $title == 'We\'re working on a new Wiki!') {
                continue;
            }
            $pathArray[] = array(
                'path' => $path,
                'title' => $title
            );
        };

        //next cycle takes 2862 seconds on php7.4-cli and 4GHz core
        //php -f tools/Tool_Example.class.php | tee tools/resources/logs.php
        foreach ($files as $path) {
            if (!in_array(pathinfo($path, PATHINFO_EXTENSION), ['htm', 'html'])) {
                continue;
            }
            $result = ['path' => $path];
            $find = false;
            foreach ($pathArray as $pathTitle) {
                $content = file_get_contents($path);
                $title = $pathTitle['title'];
                $title = str_replace(' ', '_', $title);

                //$result = preg_replace('/<a[^>]+href="([^"]+title='.$title.'[^"]+)"[^>]+>/u', $content, $r);
                $preg = '/<a[^>]+href="([^"]+title='.trim(preg_quote($title, '/'), '/').'[^"]*)"[^>]+>/u';


                $r1 =preg_match($preg, $content, $r);
                if ($r1) {
                    $find = true;
                    if ($r1) {
                        print_r($r);
                        print PHP_EOL;
                    }
                    $result['title'][] = $pathTitle;
                }
            }
            print $path.PHP_EOL;
            if ($find) {
                $resultArray[] = $result;
            }
        };
        var_export($resultArray);

        ModeService::Get()->debug('Tool running in debug mode!');
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