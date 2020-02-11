<?php declare(strict_types = 1);

require_once(__DIR__. '/Item.php');

class GenTree
{
    const DELIMITER = ';';
    const MAX_LINES = 20000;
    const BUFFER_LENGTH = 2048; /* line length*/
    const PARENT_COLUMN = 2; /* parent column array index */
    const ROOT_KEY = 'root';
    protected $tree;
    protected $csvData;
    protected $json;

    /**
     * GenTree constructor.
     */
    function __construct()
    {
        $this->tree = [];
        $this->csvData = [];
        $this->json = '';
    }

    /**
     * @param string $fileName
     * @param string $delimiter
     * @return bool
     */
    public function loadCSV(string $fileName, string $delimiter = self::DELIMITER)
    {
        $loadSuccess = FALSE;
        $l = 1;
        if (($f = fopen($fileName, "r")) !== FALSE)
        {
            $loadSuccess = TRUE;
            while (($data = fgetcsv($f, self::BUFFER_LENGTH, $delimiter)) !== FALSE)
            {
                if ($l > 1)
                {
                    $key = $data[self::PARENT_COLUMN];
                    if ($key == "") $key = self::ROOT_KEY;

                    if (!isset($this->csvData[$key])) $this->csvData[$key] = [];
                    array_push($this->csvData[$key], $data);
                }
                $l++;
                if ($l > self::MAX_LINES) {
                    $loadSuccess = FALSE;
                    printf("File contains more than %d lines\n", self::MAX_LINES);
                    break;
                }
            }
            fclose($f);
        }
        return $loadSuccess;
    }

    /**
     * @param string $fileName
     */
    public function saveJson(string $fileName)
    {
        if ($this->json === '') return;
        $f = fopen($fileName, 'w');
        fwrite($f, $this->json);
        fclose($f);
    }

    /**
     * @param bool $pretty
     * @return false|string
     */
    public function getJson(bool $pretty = false)
    {
        return json_encode($this->tree, JSON_UNESCAPED_UNICODE | ($pretty ? JSON_PRETTY_PRINT : 0));
    }

    /** Build tree/json. $pretty - set json pretty print
     * @param bool $pretty
     */
    public function build($pretty = false)
    {
        $this->addNode($this->csvData[self::ROOT_KEY]);
        $this->json = $this->getJson($pretty);
    }

    /**
     * @param array $node
     * @param Item|NULL $parent
     */
    protected function addNode(array &$node, Item $parent = NULL)
    {
        foreach ($node as $d)
        {
            $i = new Item($d);
            if ($i->parent == null)
            {
                $this->tree[] = $i;
                $child = end($this->tree);
            }
            else {
                if ($parent->itemName !== $i->parent)
                {
                    $i->parent = $parent->itemName;
                }
                $child = $parent->addChild($i);
            }
            if (isset($this->csvData[$i->itemName]))
            {
                $this->addNode($this->csvData[$i->itemName], $child);
            }
            else if ($i->getRelation() != "")
            {
                if (isset($this->csvData[$i->getRelation()]))
                {
                    $this->addNode($this->csvData[$i->getRelation()], $child);
                }
            }
        }
    }
}
