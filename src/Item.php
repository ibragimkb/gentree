<?php declare(strict_types = 1);

class Item
{
    public $itemName;
    protected $type;
    public $parent;
    protected $relation;
    public $children;

    /**
     * Item constructor.
     * @param array $data
     */
    function __construct(array &$data)
    {
        $this->itemName = &$data[0];
        $this->type = &$data[1];
        $this->parent = $data[2] == "" ? null : $data[2];
        $this->relation = &$data[3];
        $this->children = [];
    }

    /**
     * @param Item $v
     * @return mixed
     */
    public function addChild(Item &$v)
    {
        array_push($this->children, $v);
        return end($this->children);
    }

    /**
     * @return mixed
     */
    public function getRelation()
    {
        return $this->relation;
    }
}
