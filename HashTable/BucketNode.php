<?php
/**
 * User: keith.wang
 * Date: 17-6-18
 */
class BucketNode
{
    public $key;
    public $value;
    public $nextNode;
    public function __construct($key, $value, $nextNode = NULL)
    {
        $this->key = $key;
        $this->value = $value;
        $this->nextNode = $nextNode;
    }
}

