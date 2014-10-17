<?php

namespace Icybee\Modules\Taxonomy\Terms;

use ICanBoogie\ActiveRecord\Model;
use ICanBoogie\Module\Descriptor;

return array
(
	Descriptor::CATEGORY => 'organize',
	Descriptor::DESCRIPTION => 'Manage vocabulary terms',

	Descriptor::MODELS => array
	(
		'primary' => array
		(
			Model::IMPLEMENTING => array
			(
				array('model' => 'taxonomy.vocabulary/primary')
			),

			Model::SCHEMA => array
			(
				'fields' => array
				(
					'vtid' => 'serial',
					'vid' => 'foreign',
					'term' => 'varchar',
					'termslug' => 'varchar',
					'weight' => array('integer', 'unsigned' => true)
				)
			)
		),

		'nodes' => array
		(
			Model::ALIAS => 'term_node',
			Model::ACTIVERECORD_CLASS => 'ICanBoogie\ActiveRecord',
			Model::CLASSNAME => 'ICanBoogie\ActiveRecord\Model',
			Model::IMPLEMENTING => array
			(
				array('model' => 'taxonomy.terms/primary')
			),

			Model::SCHEMA => array
			(
				'fields' => array
				(
					'vtid' => array('foreign', 'primary' => true),
					'nid' => array('foreign', 'primary' => true),
					'weight' => array('integer', 'unsigned' => true)
				)
			)
		)
	),

	Descriptor::NS => __NAMESPACE__,
	Descriptor::REQUIRES => array
	(
		'nodes' => '1.0',
		'taxonomy.vocabulary' => '1.0'
	),

	Descriptor::TITLE => 'Terms'
);