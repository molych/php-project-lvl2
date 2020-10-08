<?php

namespace GenDiff\Formatters\Pretty;

use function GenDiff\FunctionsTrees\getName;
use function GenDiff\FunctionsTrees\getNodeType;
use function GenDiff\FunctionsTrees\getOldValue;
use function GenDiff\FunctionsTrees\getNewValue;
use function GenDiff\FunctionsTrees\getChildren;

function space(int $deep) : string
{
    return str_repeat(" ", $deep * 4);
}


function nodeToPretty($value, $deep)
{

  $space = space($deep);
  $currentSpace = "  $space";
    if (is_array($value)) {
        $keys = array_keys($value);
        $editNode = array_map(
            function ($key) use ($value, $currentSpace, $deep) {
              $type = gettype($value[$key]);
              $name = $key;
              $oldValue = $value[$key];
              
              if($type === 'array') {
                (int) $deep +=1;
                $value = nodeToPretty($oldValue, $deep);
                return "$currentSpace     $name:$value$currentSpace";
              } else {
                return "$currentSpace     $name:$oldValue$currentSpace";
              }
            },
            $keys
        );
        $result = implode("\n", $editNode);
        return "{\n{$result}\n$currentSpace  }";
    }

    return $value;
}


function treeToPretty($tree, int $deep = 0) {

  $space = space($deep);
  $currentSpace = "  $space";
  $diffList = array_map(function ($node) use ($currentSpace , $deep) {
      $name = getName($node);
      $oldValue = getOldValue($node);
      $newValue = getNewValue($node);
      $type = getNodeType($node);
      $children = getChildren($node);

      switch($type) {
        
          case 'added':
              $oldValue = nodeToPretty($oldValue, $deep);
              return "$currentSpace+ $name:$oldValue";
          case 'deleted':
            $oldValue = nodeToPretty($oldValue, $deep);
              return "$currentSpace- $name:$oldValue";
          case 'unchange':
              return "$currentSpace  $name:$oldValue";
          case 'change':
              $oldValue = nodeToPretty($oldValue, $deep);
              $newValue = nodeToPretty($newValue, $deep);
              return "$currentSpace+ $name:$newValue\n$currentSpace- $name:$oldValue";
          case 'nested':
              $deep += 1;
              $children = treeToPretty($children,$deep);
                return "$currentSpace  $name:{\n$children\n$currentSpace  }";
          default:
                throw new \Exception("unknown type $type");
      }
      
  }, $tree);

  $result = implode("\n", $diffList);
    return $result;
}
