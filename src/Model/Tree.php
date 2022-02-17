<?php

namespace Src\Model;

class Tree
{
    /**
     * @return array
     */
    public function getTree($oldTree): array
    {
        $tree = [];
        $tree = $this->buildTree($oldTree, $tree);
        return $tree;
    }

    /**
     * @param $oldTree
     * @param $tree
     * @param $parentId
     * @param $level
     * @return array
     */
    public function buildTree($oldTree, $tree, $parentId = NULL, $level = 0): array
    {
        if (isset($parentId)) {
            foreach ($oldTree as $node) {
                if ($node['parent_id'] == $parentId) {
                    $tmpNodeArr = $node;
                    $tmpNodeArr['level'] = $level + 1;
                    array_push($tree, $tmpNodeArr);
                    $tree = $this->buildTree($oldTree, $tree, $tmpNodeArr['id'], $tmpNodeArr['level']);
                }
            }
            return $tree;
        } else {
            $tmpNodeArr = $oldTree[0];
            $tmpNodeArr['level'] = 0;
            array_push($tree, $tmpNodeArr);
            $tree = $this->buildTree($oldTree, $tree, $tmpNodeArr['id'], $tmpNodeArr['level']);
        }
        return $tree;
    }
}

?>