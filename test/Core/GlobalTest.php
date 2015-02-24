<?php


namespace Core;


class GlobalTest extends TestCase {

    public function testReferencesToArchipadOrArchiweb () {

        $root = __DIR__ . '/../..';

        $directory = new \RecursiveDirectoryIterator($root);
        $filter = new \RecursiveCallbackFilterIterator($directory, function (\SplFileInfo $current, $key, $iterator) {

            $blacklist = ['doc', 'coverage', 'vendor'];

            // Skip hidden files and directories.
            if ($current->getFilename()[0] === '.') {
                return false;
            }
            if (in_array($current->getFilename(), $blacklist)) {
                return false;
            }

            if ($current->getPathname() == __FILE__) {
                return false;
            }

            return true;

        });
        $iterator = new \RecursiveIteratorIterator($filter);
        foreach ($iterator as $info) {

            $this->assertNotRegExp('/(archipad)|(archiweb)/', file_get_contents($info->getPathname()),
                                   $info->getPathname());

        }

    }

    public function testEndingPhpTag () {

        $root = __DIR__ . '/../..';

        $directory = new \RecursiveDirectoryIterator($root);
        $filter = new \RecursiveCallbackFilterIterator($directory, function (\SplFileInfo $current, $key, $iterator) {

            $blacklist = ['doc', 'coverage', 'vendor'];

            if ($current->isFile()) {
                if (strtolower($current->getExtension()) == 'php') {
                    return true;
                }

                return false;
            }

            if ($current->getFilename()[0] === '.') {
                return false;
            }
            if (in_array($current->getFilename(), $blacklist)) {
                return false;
            }

            return true;

        });
        $iterator = new \RecursiveIteratorIterator($filter);
        foreach ($iterator as $info) {

            $this->assertNotRegExp('/\?>\s*$/', file_get_contents($info->getPathname()),
                                $info->getPathname());

        }

    }

}