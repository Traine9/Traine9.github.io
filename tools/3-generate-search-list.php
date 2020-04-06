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
            $pathArray[$title] = array(
                'path' => pathinfo($path, PATHINFO_BASENAME),
                'title' => $title
            );
        };
        $path = 'Unreal Engine Wiki/mediawikiv2-website-prod05.ol.epicgames.net/search.json';
        file_put_contents($path, json_encode(array_values($pathArray)));
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