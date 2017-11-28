<?php

/*
 * The MIT License
 *
 * Copyright 2017 nehemiah.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Seasmhach\Nehemiah;
use Seasmhach\Nehemiah\DataObject;
use ArrayIterator;
use PDO;

/**
 * @todo Take stuff out that has nothing to-do with reverse engineering the DB!
 */
class ReverseEngineerDB extends ArrayIterator {
	private $integer_types = ['TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT'];
	private $floating_point_types = ['FLOAT', 'DOUBLE', 'DECIMAL'];

	protected $database = '';

	public function __construct(string $database) {
		$this->database = $database;

		$pdo = DataObject::pdo_instance($this->database);
		$tables = $pdo->query("SHOW TABLES");
		$collection = [];

		while($table_name = $tables->fetch(PDO::FETCH_COLUMN)) {
			$definition = $pdo->query("SHOW CREATE TABLE `$table_name`");
			$collection[$table_name] = $collection[$table_name] ?? [];
			$collection[$table_name]['create_table'] = $definition->fetch(PDO::FETCH_ASSOC)['Create Table'];
			$collection[$table_name]['namespace'] = str_replace(' ', '', ucwords(str_replace('_', ' ', $table_name)));

			$this->parse_columns($table_name, $collection[$table_name]);
			$this->parse_indexes($collection[$table_name]);
		}

		parent::__construct($collection);
	}

	protected function parse_columns(string $table_name, array &$definition) {
		$pdo = DataObject::pdo_instance($this->database);
		$sql = "SHOW FULL COLUMNS FROM `$table_name`";
		$result = $pdo->query($sql);
		$definition['columns'] = $definition['primary_key'] = [];

		while ($column = $result->fetch(PDO::FETCH_ASSOC)) {
			$definition['columns'][$column['Field']] = [
				'pk' => $column['Key'] === 'PRI' ? true : false,
				'null' => $column['Null'] === 'YES' ? true : false,
				'default' => $column['Default'],
				'auto_increment' => (strpos($column['Extra'], 'auto_increment') !== false),
				'comment' => $column['Comment'],
			];

			$this->parse_column_type($column['Type'], $definition['columns'][$column['Field']]);

			if ($definition['columns'][$column['Field']]['pk']) {
				$definition['primary_key'][] = $column['Field'];
			}
		}
	}

	protected function parse_column_type(string $type, array &$definition) {
		$matches = [];

		preg_match("/\((.*?)\)/", $type, $matches);

		$definition['unsigned'] = (strpos($type, 'unsigned') !== false);
		$definition['type'] = explode('(', $type)[0];
		$definition['value'] = $matches[1] ?? null;

		if (in_array(strtoupper($definition['type']), $this->integer_types)) {
			$definition['data_type'] = 'int';
		} elseif (in_array(strtoupper($definition['type']), $this->floating_point_types)) {
			$definition['data_type'] = 'float';
		} else {
			$definition['data_type'] = 'string';
		}
	}

	protected function parse_indexes(array &$definition) {
		$fk_matches = $unique_matches = $definition['foreign_keys'] = $definition['unique_keys'] = [];

		preg_match_all("/CONSTRAINT\s*\`(.*?)\`\s*FOREIGN\s*KEY\s*\(\`(.*?)\`\)\s*REFERENCES\s*`(.*?)`\s*\(\`(.*?)\`\)/is", $definition['create_table'], $fk_matches, PREG_SET_ORDER);
		preg_match_all("/UNIQUE\s*KEY\s*\`(.*?)\`\s*\((.*?)\)/is", $definition['create_table'], $unique_matches, PREG_SET_ORDER);

		foreach ($fk_matches as $match) {
			$foreign_key = [
				'identifier' => $match[1],
				'table' => $match[3],
				'columns' => [$match[2] => $match[4]],
			];

			$definition['foreign_keys'][] = $foreign_key;
		}

		foreach ($unique_matches as $match) {
			$definition['unique_keys'][$match['1']] = str_getcsv($match[2], ',', '`');
		}
	}
}
