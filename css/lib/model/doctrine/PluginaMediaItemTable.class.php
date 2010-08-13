<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PluginaMediaItemTable extends Doctrine_Table
{
  public function getLuceneIndex()
  {
    return aZendSearch::getLuceneIndex($this);
  }
   
  public function getLuceneIndexFile()
  {
    return aZendSearch::getLuceneIndexFile($this);
  }

  public function searchLucene($luceneQuery)
  {
    return aZendSearch::searchLucene($this, $luceneQuery);
  }
  
  public function rebuildLuceneIndex()
  {
    return aZendSearch::rebuildLuceneIndex($this);
  }
  
  public function optimizeLuceneIndex()
  {
    return aZendSearch::optimizeLuceneIndex($this);
  }
  
  public function addSearchQuery(Doctrine_Query $q = null, $luceneQuery)
  {
    return aZendSearch::addSearchQuery($this, $q, $luceneQuery);
  }
  
  static public function getDirectory()
  {
    return aFiles::getUploadFolder('media_items');
  }

  // Returns items for all ids in $ids. If an item does not exist,
  // that item is not returned; this is not considered an error.
  // You can easily compare count($result) to count($ids). 
  static public function retrieveByIds($ids)
  {
    if (!count($ids))
    {
      // WHERE freaks out over empty lists. We don't.
      return array();
    }
    if (count($ids) == 1)
    {
      if (!$ids[0])
      {
        // preg_split and its ilk return a one-element array
        // with an empty string in it when passed an empty string.
        // Tolerate this.
        return array();
      }
    }
    $q = Doctrine_Query::create()->
      select('m.*')->
      from('aMediaItem m')->
      whereIn("m.id", $ids);
    aDoctrine::orderByList($q, $ids);
    return $q->execute();
  }
  static public $mimeTypes = array(
    "gif" => "image/gif",
    "png" => "image/png",
    "jpg" => "image/jpeg",
    "pdf" => "application/pdf"
  );
  
  // Returns a query matching media items satisfying the specified parameters, all of which
  // are optional:
  //
  // tag
  // search
  // type (video or image)
  // user (a username, to determine access rights)
  // aspect-width and aspect-height (returns only images with the specified aspect ratio)
  // minimum-width
  // minimum-height
  // width
  // height 
  // ids
  //
  // Parameters are passed safely via wildcards so it should be OK to pass unsanitized
  // external API inputs to this method.
  //
  // 'ids' is an array of item IDs. If it is present, only items with one of those IDs are
  // potentially returned.
  //
  // If 'search' is present, results are returned in descending order by match quality.
  // Otherwise, if 'ids' is present, results are returned in that order. Otherwise,
  // results are returned newest first.
  
  static public function getBrowseQuery($params)
  {
    $query = Doctrine_Query::create();
    // We can't use an alias because that is incompatible with getObjectTaggedWithQuery
    $query->from('aMediaItem');
    if (isset($params['ids']))
    {
      $query->select('aMediaItem.*');
      aDoctrine::orderByList($query, $params['ids']);
      $query->andWhereIn("aMediaItem.id", $params['ids']);
    }
    if (isset($params['tag']))
    {
      $query = TagTable::getObjectTaggedWithQuery(
        'aMediaItem', $params['tag'], $query);
    }
    if (isset($params['type']))
    {
      $query->andWhere("aMediaItem.type = ?", array($params['type']));
    }
    if (isset($params['allowed_categories']))
    {
      $query->innerJoin('aMediaItem.MediaCategories mc1 WITH mc1.id IN (' . implode(',', aArray::getIds($params['allowed_categories'])) . ')');
    }
    if (isset($params['category']))
    {
      $query->innerJoin('aMediaItem.MediaCategories mc2 WITH mc2.slug = ?', array($params['category']));
    }
    if (isset($params['search']))
    {
      $query = Doctrine::getTable('aMediaItem')->addSearchQuery($query, $params['search']);
    }
    elseif (isset($params['ids']))
    {
      // orderBy added by aDoctrine::orderByIds
    }
    else
    {
      // Reverse chrono order if we're not ordering them by search relevance
      $query->orderBy('aMediaItem.id desc');
    }
    // This will be more interesting later
    if (!isset($params['user']))
    {
      $query->andWhere('aMediaItem.view_is_secure = false');
    }
    if (isset($params['aspect-width']) && isset($params['aspect-height']))
    {
      $query->andWhere('(aMediaItem.width * ? / ?) = aMediaItem.height', array($params['aspect-height'] + 0, $params['aspect-width'] + 0));
    }
    if (isset($params['minimum-width']))
    {
      $query->andWhere('aMediaItem.width >= ?', array($params['minimum-width'] + 0));
    }
    if (isset($params['minimum-height']))
    {
      $query->andWhere('aMediaItem.height >= ?', array($params['minimum-height'] + 0));
    }
    if (isset($params['width']))
    {
      $query->andWhere('aMediaItem.width = ?', array($params['width'] + 0));
    }
    if (isset($params['height']))
    {
      $query->andWhere('aMediaItem.height = ?', array($params['height'] + 0));
    }
    return $query;
  }
  
  static public function getAllTagNameForUserWithCount()
  {
    // Retrieves only tags relating to media items this user is allowed to see
    $q = NULL;
    if (!sfContext::getInstance()->getUser()->isAuthenticated())
    {
      $q = Doctrine_Query::create()->from('Tagging tg, tg.Tag t, aMediaItem m');
      // If you're not logged in, you shouldn't see tags relating to secured stuff
      // Always IS FALSE, never = FALSE
      $q->andWhere('m.id = tg.taggable_id AND ((m.view_is_secure IS NULL) OR (m.view_is_secure IS  FALSE))');
    }
    return TagTable::getAllTagNameWithCount($q, 
      array("model" => "aMediaItem"));
  }
  
  // Retrieves media items matching the supplied array of ids, in the same order as the ids
  // (a simple whereIn does not do this). We must use an explicit select when using
  // aDoctrine::orderByList.
  
  public function findByIdsInOrder($ids)
  {
    if (empty($ids))
    {
      // Doctrine doesn't generate any clause at all for WHERE IN if an array if false. This is a bug, but
      // it doesn't seem to be getting fixed at the Doctrine level
      return Doctrine::getTable('aMediaItem')->createQuery('m')->select('m.*')->where('1 = 0');
    }
    $q = Doctrine::getTable('aMediaItem')->createQuery('m')->select('m.*')->whereIn('m.id', $ids);
    // Don't forget to put them in order!
    return aDoctrine::orderByList($q, $ids)->execute();
  }
}