<?php
class Tool_Example {

    public function process() {
        $resultArray = include 'resources/linkArray.php';
        foreach ($resultArray as $item) {
            $editPath = $item['path'];
            $content = file_get_contents($editPath);
            foreach ($item['title'] as $item2) {
                $path = $item2['path'];
                $title = $item2['title'];
                $title = str_replace(' ', '_', $title);
                $title = trim(preg_quote($title, '/'), '/');
                $htmlFile = pathinfo($path, PATHINFO_BASENAME);
                $pregReplace =
                    /** @lang RegExp */
                    '/(<a[^>]+href=")([^"]+\.php)(.title='.$title.'[^"]*"[^>]+>)/u';
                $tmp = preg_replace($pregReplace, "$1$htmlFile$3", $content);
                if ($tmp) {
                    $content = $tmp;
                }

            }
            file_put_contents($editPath, $content);

        }
    }
}
$x = new Tool_Example();
$x->process();