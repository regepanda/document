<?php 

require_once "./BucketNode.php";

class HashTable
{
    private $buckets;
    private $size = 10;

    public function __construct()
    {
        $this->buckets = new SplFixedArray($this->size);
    }

    private function hashfunc($key)
    {
        $strlen = strlen($key);
        $hashval = 0;
        for($i = 0; $i < $strlen; $i++)
        {
            $hashval += ord($key{$i});
        }
        return $hashval % $this->size;
    }

//    public function insert($key, $val)
//    {
//        $index = $this->hashfunc($key);
//        $this->buckets[$index] = $val;
//    }

    public function insert($key, $val)
    {
        $index = $this->hashfunc($key);

        if(isset($this->buckets[$index]))
        {
            $newNode = new BucketNode($key, $val, $this->buckets[$index]);
        }
        else
        {
            $newNode = new BucketNode($key, $val, NULL);
        }

        $this->buckets[$index] = $newNode;
    }

//    public function find($key)
//    {
//        $index =$this->hashfunc($key);
//        return $this->buckets[$index];
//    }

    public function find($key)
    {
        $index = $this->hashfunc($key);
        $current = $this->buckets[$index];
        while (isset($current))
        {
            if ($current->key == $key)
            {
                return $current->value;
            }
            $current = $current->nextNode;
        }
        return NULL;
    }
}

$hashTable = new HashTable();
$hashTable->insert('key1', 'value1');
$hashTable->insert('key12', 'value12');
$hashTable->insert('key12', 'value123');
echo $hashTable->find('key1');
echo $hashTable->find('key12');

