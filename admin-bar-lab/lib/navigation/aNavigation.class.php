<?php

abstract class aNavigation
{
  
  public static $tree = null;
  public static $hash = null;
  
  // Functional testing reuses the same PHP session, we must
  // accurately simulate a new one. This method is called by
  // an event listener in aTools. Add more calls there for other
  // classes that do static caching
  public static function simulateNewRequest()
  {
    if (sfConfig::get('app_a_many_pages', false))
    {
      
    }
    self::$tree = null;
    self::$hash = null;
  }
  
  protected abstract function buildNavigation();
  
  
  public function initializeTree()
  {
    if(!isset(self::$tree))
    {
      $root = aPageTable::retrieveBySlug('/');
      
      $rootInfo['id'] = $root['id'];
      $rootInfo['lft'] = $root['lft'];
      $rootInfo['rgt'] = $root['rgt'];
      $rootInfo['title'] = $root['title'];
      $rootInfo['slug'] = $root['slug'];
      
      $tree = $root->getTreeInfo(false);
      $rootInfo['children'] = $tree;
      self::$tree = array($rootInfo);
      self::createHashTable(self::$tree, $rootInfo);
    }
  }
  
  public function createHashTable($tree, $parent)
  {
    foreach ( $tree as $node )
    {
      $node['parent'] = $parent;
      self::$hash[$node['slug']] = $node;
      if(isset($node['children']))
        $this->createHashTable($node['children'], $node);
    } 
  }
  
  public function applyCSS(&$tree, &$node)
  {
    $node['class'] = $this->cssClass;      
    if(self::isAncestor($node, $this->activeInfo))
    {
      //We need to set this nodes peers to have the ancestor-peer class
      foreach($tree as &$peer)
      {
        $peer['class'] = @$peer['class'].' ancestor-peer';
      } 
      //This page is an ancestor so set the class
      $node['class'] = $node['class'].' ancestor';
      }
    else if($node['id'] == $this->activeInfo['id'])
    {
      //We need to set this nodes peer to have the peer class
      foreach($tree as &$peer)
      {
        $peer['class'] = @$peer['class'].' peer';
      }
      //This node is the current so set the class
      $node['class'] = $node['class'].' a-current-page';
    }
  }
  
  
  public function __construct($root, $active, $options = array())
  {
    $this->user = sfContext::getInstance()->getUser();
    $this->livingOnly = !(aTools::isPotentialEditor() &&  sfContext::getInstance()->getUser()->getAttribute('show-archived', true, 'apostrophe'));
    
    $this->root = $root;
    $this->active = $active;
    $this->options = $options;
    
    $this->initializeTree();

    $this->buildNavigation();
  }
  
  public static function isAncestor($node1, $node2)
  {
    return $node1['lft'] < $node2['lft'] && $node1['rgt'] > $node2['rgt'];
  }
  
  public static function isChild($node1, $node2)
  {
    return $node1['lft'] > $node2['lft'] && $node1['rgt'] < $node2['rgt'] && $node1['lvl'] - $node2['lvl'] == 1;
  }

}