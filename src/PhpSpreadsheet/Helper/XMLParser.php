<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 06.06.18
 * Time: 16:10
 */

namespace PhpOffice\PhpSpreadsheet\Helper;

use PhpOffice\PhpSpreadsheet\Helper\XMLParser\FakeSimpleXMLElement;


class XMLParser extends \XMLReader {
    private $onNodeEvent;

    public function __construct($onNodeEvent = null)
    {
        $this->setOnNodeEvent($onNodeEvent);
    }

    public function setOnNodeEvent($fn)
    {
        $this->onNodeEvent = $fn;
    }

    private function onNode($rootNode, $skip)
    {
        $on = $this->onNodeEvent;
        if ($on !== null) {
            $eventCall = true;
            if (is_array($skip)) {
                $t = $rootNode;
                $i = 0;
                while ($t !== null && $i < count($skip)) {
                    if ($t->getName() != $skip[$i])
                        break;
                    ++$i;
                    $t = $t->getChild();
                }
                if ($i == count($skip))
                    $eventCall = false;
            }
            if ($eventCall)
                $on($rootNode);
        }
    }

    public function parse($skip = null)
    {
        $rootNode = null;
        $current = null;
        $nameCounters = [];
        while ($this->read()) {
            switch ($this->nodeType) {
                case \XMLReader::ELEMENT:
                    $nodeName = $this->name;
                    $value = $this->value;
                    $isEmpty = $this->isEmptyElement;
                    $attribs = [];
                    if ($this->hasAttributes)
                        while ($this->moveToNextAttribute())
                            $attribs[$this->localName] = $this->value;
                    if (!isset($nameCounters[$this->depth]))
                        $nameCounters[$this->depth] = [];
                    if (!isset($nameCounters[$this->depth][$nodeName]))
                        $nameCounters[$this->depth][$nodeName] = 0;
                    else
                        ++$nameCounters[$this->depth][$nodeName];
                    $index = $nameCounters[$this->depth][$nodeName];
                    $newNode = new FakeSimpleXMLElement($current, $nodeName, $index);
                    $newNode->setAttributes($attribs);
                    $newNode->setValue($value);
                    $newNode->setEmpty($isEmpty);
                    if ($current === null)
                        $rootNode = $newNode;
                    else
                        $current->setChild($newNode);
                    $current = $newNode;
                    if ($isEmpty && $current !== null) {
                        $this->onNode($rootNode, $skip);
                        $current = $current->getParent();
                        if ($current !== null)
                            $current->removeChild();
                    }
                    break;
                case \XMLReader::END_ELEMENT:
                    $this->onNode($rootNode, $skip);
                    array_splice($nameCounters, $this->depth);
                    if ($current !== null) {
                        $current = $current->getParent();
                        if ($current !== null)
                            $current->removeChild();
                    }
                    break;
                case \XMLReader::CDATA:
                case \XMLReader::TEXT:
                    if ($current !== null)
                        $current->setValue($this->value);
                    break;
            }
        }
    }
};
