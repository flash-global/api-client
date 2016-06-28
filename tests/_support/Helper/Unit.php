<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Unit extends \Codeception\Module
{

    public function assertThrows($callback, $exception = 'Exception', $message = '')
    {
        $function = function () use ($callback, $exception) {
            $getAncestors = function($e) {

                for ($classes[] = $e; $e = get_parent_class ($e); $classes[] = $e);
                return $classes;

            };

            try {
                $callback();
                return false;
            }
            catch (\Exception $e) {
                if (get_class($e) == $exception or in_array($e, $getAncestors($e))) {
                    return true;
                }
                return false;
            }
        };
        $this->assertTrue($function(), $message);
    }

}
