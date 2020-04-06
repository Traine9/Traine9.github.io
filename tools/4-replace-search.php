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
            $content = preg_replace('/<form[^>]*id="searchform"[^>]*>/u', '<form id="searchform" action="search.html" method="GET">', $content);
            if ($content) {
                file_put_contents($path, $content);
                print $path.PHP_EOL;
            }
        };
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