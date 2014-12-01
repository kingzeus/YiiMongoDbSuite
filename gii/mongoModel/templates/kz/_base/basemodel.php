<?php
/**
 * This is the template for generating the model class of a specified table.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $collectionName: the mongo collection name to use
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $relations: list of relations (name=>relation declaration)
 * - $primaryKey: primary key name
 */
?>
<?php echo "<?php\n"; ?>

/**
 * This is the MongoDB Document model class based on table "<?php echo $tableName; ?>".
 */
abstract class <?php echo $this->baseModelClass; ?> extends <?php echo $this->baseClass."\n"; ?>
{
<?php foreach($columns as $column): ?>
	public <?php echo '$'.$column->name.";\n"; ?>
<?php endforeach; ?>

	/**
	 * Returns the static model of the specified AR class.
	 * @return <?php echo $modelClass; ?> the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * returns the primary key field for this model
	 */
	public function primaryKey()
	{
		return <?php var_export($primaryKey); ?>;
	}

	/**
	 * @return string the associated collection name
	 */
	public function getCollectionName()
	{
		return '<?php echo $collectionName; ?>';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
<?php foreach($rules as $rule): ?>
			<?php echo $rule.",\n"; ?>
<?php endforeach; ?>
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('<?php echo implode(', ', array_keys($columns)); ?>', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
<?php foreach($labels as $name=>$label): ?>
<?php if($label === null): ?>
			<?php echo "'{$name}' => null,\n"; ?>
<?php else: ?>
			<?php echo "'{$name}' => {$label},\n"; ?>
<?php endif; ?>
<?php endforeach; ?>
		);
	}

	public function search($pagination = array()) {
		$criteria = new EMongoCriteria;

<?php foreach($columns as $name=>$column): ?>
        if($this-><?php echo $name; ?>!==null && strlen($this-><?php echo $name; ?>)>0)
        <?php if($column->type == 'integer'): ?>
		  $criteria->addCond('<?php echo $name; ?>','eq',new MongoInt64( $this-><?php echo $name; ?>));
		<?php else: ?>
		  $criteria->addCond('<?php echo $name; ?>','eq', $this-><?php echo $name; ?>);
		<?php endif; ?>
<?php endforeach; ?>
		
		$config = array(
				'criteria' => $criteria,
			);
		
		if(is_array($pagination)&& count($pagination)>0)
		{
			$config['pagination'] = $pagination;
		}
		return new EMongoDocumentDataProvider($this, $config);
	}
}