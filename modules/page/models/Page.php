<?php


if (Yii::app()->getModule('page')->events)
{
	Yii::import('aiajaya.modules.event.models.Event');
}


/**
 * This is the model class for table "{{page}}".
 */
class Page extends CActiveRecord
{
	/**
	 * The followings are the available columns in table '{{page}}':
	 * @var integer $id
	 * @var string $key
	 * @var string $meta_keyword
	 * @var string $meta_description
	 * @var string $meta_title
	 * @var string $text
	 */

	static $replaceArray;
	public function behaviors()
	{

		if (!self::$replaceArray)
		{
			$replaceArray = array(
				 '{block1}'=>array('void()', array('controller')),
				 '{neueSeite}'=>array('setPageBreak()', array('controller')),
				 '"bilder/'=>array('getImageUrl("\"")', array('controller')),
				 '\'bilder/'=>array('getImageUrl("\'")', array('controller')),
			);

			if (Yii::app()->getModule('page')->team && $models = Team::model()->findAll())
			{
				foreach ($models as $model)
					$replaceArray['{contact_'.$model->key.'}'] = array("getContact('".$model->key."');", array('this'));
				foreach ($models as $model)
				{
					$alias = array($model->name);
					$alias[] = htmlentities($model->name);
					if ($model->name == 'Milam M. Horn')
						$alias[] = 'Milam Horn';
					foreach ($alias as $name)
					{
						// don't add > because it might match the last php closing tag 
						$safeHtml = '[a-zA-Z0-9_\/<&="; ,]';
						$regex = '/('.$safeHtml.'{0,5})('.$name.')('.$safeHtml.'{0,3})/';
						// don't remove the ; it has a special meaning
						$replaceArray[$regex] = array("teamLink('\\2', '".$model->key."', '\\1', '\\3');", array('this'));
					}
				}
			}
			self::$replaceArray = $replaceArray;
		}

		$return = array(
			'ETemplateBehavior' => array('class' => 'aiajaya.extensions.ETemplateBehavior',
				'replaceArray'=>self::$replaceArray,
				'basePath'=>array('application', 'runtime', 'pagetemplate'),
				'contentVar'=>'text',
			)
			);
		if (Yii::app()->params['enableTags']) {

			$return['tags'] = array(
				'class' => 'aiajaya.extensions.taggable-behavior.ETaggableBehavior',
				// Table where tags are stored
				'tagTable' => '{{Tag}}',
				// Cross-table that stores tag-model connections.
				// By default it's your_model_tableTag
				'tagBindingTable' => '{{PageTag}}',
				// Foreign key field field in cross-table.
				// By default it's your_model_tableId
				'modelTableFk' => 'page_id',
				// tagTableCondition - empty by default. Can be used in cases where e.g. the tag is composed of 
				// two fields and a custom search expression is needed to find the tag. Example for user table:
				// 'tagTableCondition' => new CDbExpression("CONCAT(t.name,' ',t.surname) = :tag "),
				// Tag table PK field
				'tagTablePk' => 'id',
				// Tag name field
				'tagTableName' => 'name',
				// Tag counter field
				// if null (default) does not write tag counts to DB
				'tagTableCount' => null,//'count',
				// Tag binding table tag ID
				'tagBindingTableTagId' => 'tagId',
				// Caching component ID. If false don't use cache.
				// Defaults to false.
				'cacheID' => false,//'cache',

				// Save nonexisting tags.
				// When false, throws exception when saving nonexisting tag.
				'createTagsAutomatically' => true,

				// Default tag selection criteria
				'scope' => array(
					//'condition' => ' t.user_id = :user_id ',
					//'params' => array( ':user_id' => Yii::app()->user->id ),
				),

				// Values to insert to tag table on adding tag
				'insertValues' => array(
					//'user_id' => Yii::app()->user->id,
				),
			);
		}
		return $return;
	}


	protected $teamLinks = array();

	public function teamLink($match, $key, $match5Before, $match3After)
	{
		$orig = $match5Before.$match.$match3After;
		if (strpos($match5Before, '<h') !== false || strpos($match3After, '</h') !== false)
			return $orig;
		if (strpos($match3After, '</a') !== false)
			return $orig;
		if (substr($match5Before, -1) == '"')
			return $orig;
		if (empty($match5Before) && empty($match3After))
			return $orig;
		$this->teamLinks[] = $key;
		return $match5Before . Team::model()->findCachedByKey($key)->getTeamLink() . $match . $match3After;
	}

	public function getContact($key)
	{
		$this->teamLinks[] = $key;
		return Team::model()->findCachedByKey($key)->getContact();
	}		

	public function afterSave()
	{
		parent::afterSave();
		$this->getReplacedTemplate(array('controller'=>Yii::app()->controller));
		$links = array_unique($this->teamLinks);
		foreach ($this->teamPages as $t)
			$t->delete();
		if ($this->active)
		{
			foreach ($links as $key)
			{
				$tm = Team::model()->findCachedByKey($key);
				$tId = $tm->id;
				$t = new TeamPage();
				$t->attributes = array('page_id'=>$this->id, 'team_id'=>$tId);
				$t->save();
			}
		}
		Yii::log($this->text, 'info', 'seite gespeichert');
	}

	public function findAllForTeam($ids)
	{
		$pages = Page::model()->findallByAttributes(array('id'=>$ids));
		foreach ($pages as $k=>$p)
		{
			if (in_array($p->key, array('impressum', 'lageplan', 'gutscheinverkauf', 'links', 'ueber_uns', 'startseite')))
				unset($pages[$k]);
		}
		return $pages;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @return Page the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{page}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('key', 'required'),
			array('active, commentable, commentName, meta_keyword, meta_description, meta_title, text', 'safe'),
			array('key', 'length', 'max'=>50),
			array('meta_keyword, meta_description, meta_title', 'length', 'max'=>255),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'id',
			'commentable' => 'Kommentierbar',
			'commentName' => 'angezeigter Name bei Kommentaren',
			'active' => 'aktiv',
			'key' => 'Schlüsselwort (intern)',
			'meta_keyword' => 'Meta Schlüsselwörter',
			'meta_description' => 'Meta Beschreibung',
			'meta_title' => 'Seitentitel',
			'text' => 'Text',
		);
	}

	public function scopes()
	{
		return array(
			'sitemap'=>array('select'=>'`key`', 'condition'=>'active=1 AND `key` NOT IN ("termine", "kontakt", "lageplan", "impressum")',),
		);
	}

	protected static $nm;
	protected static $angebote;
	public function getPreKey()
	{
		static $key = array();
		if (!self::$nm)
		{
			self::$nm = new NavigationMenu();
			self::$angebote = self::$nm->getAngebote();
		}
		if (!isset($key[$this->id]))
		{
			$item = self::$nm->getEntryByPageKey(array(self::$angebote), $this->key);
			if ($item && isset($item['url']) && isset($item['url']['preKey']))
				$key[$this->id] = $item['url']['preKey'];
			else
				$key[$this->id] = false;
		}
		return $key[$this->id];
	}

	public function getCategory()
	{
		static $cache = array();
		if (!isset($cache[$this->id]))
		{
			$model = Categorypage::model()->findByAttributes(array('pagekey'=>$this->key));
			$cache[$this->id] = $model;
		}
		return $cache[$this->id];
	}

	public function getUrl()
	{
		if ($this->getPreKey())
			return array('/page/page/get', 'key'=>$this->key, 'preKey'=>$this->getPreKey());
		return array('/page/page/get', 'key'=>$this->key);
	}

	public function relations()
	{
		return array(
			'comments' => array(self::HAS_MANY, 'Comment', 'pageId'),
			'commentCount' => array(self::STAT, 'Comment', 'pageId'),
			'teamPages' => array(self::HAS_MANY, 'TeamPage', 'page_id'),
			'categoryPages' => array(self::HAS_MANY, 'Categorypage', '', 'foreignKey' => array('pagekey'=>'key')),
			'events' => array(self::HAS_MANY, 'Event', 'page_id',
				'condition'=>'`to`>'.(time()-60*30), 'order'=>'`from` ASC'),
		);
	}

	/**
	 * get all related comments for the model this behavior is attached to
	 *
	 * @return Comment[]
	 * @throws CException
	 */
	public function getComments($models = array())
	{
		$comments = Comment::model()->findAll($this->getCommentCriteria($models));
		$indexedModels = array();
		foreach ($models as $model)
			$indexedModels[$model->getPrimaryKey()] = $model;
		$indexedModels[$this->owner->getPrimaryKey()] = $this->owner;

		foreach($comments as $comment) {
			/** @var Comment $comment */
			$comment->baseModel = $indexedModels[$comment->pageId];
		}
		return $comments;
	}

	protected function getCommentCriteria($models=array())
	{
		$pks = array($this->getPrimaryKey());
		foreach($models as $m)
			$pks[] = $m->getPrimaryKey();
		$criteria = new CDbCriteria(array(
		    'condition' => "pageId IN (".implode(',',$pks).")",
			'order'=>'createDate DESC',
		));
		return $criteria;
	}

	/**
	 * returns a new comment instance that is related to the model this behavior is attached to
	 *
	 * @return Comment
	 * @throws CException
	 */
	public function getCommentInstance()
	{
		$comment = Comment::model();
		$comment->pageId = $this->owner->primaryKey;
		return $comment;
	}

}
