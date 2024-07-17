<?php
/**
 * Conditions
 *
 * @package sid
 */

namespace sid\Components;

/**
 * Class Condition
 *
 * Creates a prepared part of SQL queries
 *
 * @since 0.0.1
 */
class Condition {

	const FORMAT_STRING = '%s';
	const FORMAT_NUMBER = '%d';

	/**
	 * "Where" condition
	 *
	 * @var array
	 */
	protected array $where = array();

	/**
	 * "Like % %" condition
	 *
	 * @var array
	 */
	protected array $where_like = array();

	/**
	 * Order condition
	 *
	 * @var array
	 */
	protected array $order = array();

	/**
	 * Relation "OR" or "AND"
	 *
	 * @var string
	 */
	protected string $where_relation = 'OR';

	/**
	 * Limit condition
	 *
	 * @var int|null
	 */
	protected ?int $limit = null;

	/**
	 * Offset condition
	 *
	 * @var int|null
	 */
	protected ?int $offset = null;

	/**
	 * Return all condition in array.
	 *
	 * @return array
	 */
	public function get_conditions(): array {
		$result              = array();
		$result['where']     = $this->where;
		$result['whereLike'] = $this->where_like;
		$result['order']     = $this->order;
		$result['limit']     = $this->limit;
		$result['offset']    = $this->offset;

		return $result;
	}

	/**
	 * Set WHERE value
	 *
	 * For example $condition->setWhere("Id", "15", "%d")
	 *
	 * @param string $name - name.
	 * @param mixed  $value - value.
	 * @param string $format - it is WP data format for prepare SQL request @link
	 *               https://developer.wordpress.org/reference/classes/wpdb/prepare/ .
	 * @param string $compare - compare.
	 *
	 * @return bool
	 */
	public function set_where( string $name, $value, string $format, string $compare = '=' ): bool {
		if ( empty( $name ) && empty( $value ) ) {
			return false;
		}
		$this->where[] = array( $name, $value, $format, $compare );

		return true;
	}

	/**
	 * Set LIKE condition
	 *
	 * @param string $name - name.
	 * @param mixed  $value - value.
	 * @param string $format - format.
	 *
	 * @return bool
	 */
	public function set_where_like( string $name, $value, string $format ): bool {
		if ( empty( $name ) && empty( $value ) ) {
			return false;
		}
		$this->where_like[] = array( $name, $value, $format );

		return true;
	}

	/**
	 * Set relation condition
	 *
	 * @param string $relation - relation.
	 *
	 * @return bool
	 */
	public function set_where_relation( string $relation = 'OR' ): bool {
		if ( strcasecmp( $relation, 'AND' ) === 0 || strcasecmp( $relation, 'OR' ) === 0 ) {
			$this->where_relation = $relation;

			return true;
		}

		return false;
	}

	/**
	 * Set order
	 *
	 * @param string $column - column name.
	 * @param string $type - order type ASC|DESC.
	 *
	 * @return bool
	 */
	public function set_order( string $column, string $type = 'ASC' ): bool {
		$this->order[] = array(
			'column' => $column,
			'type'   => $type,
		);

		return true;
	}

	/**
	 * Set limit
	 *
	 * @param integer $limit - limit number.
	 */
	public function set_limit( int $limit ): void {
		$this->limit = $limit;
	}

	/**
	 * Set offset
	 *
	 * @param integer $offset - offset number.
	 */
	public function set_offset( int $offset ): void {
		$this->offset = $offset;
	}

	/**
	 * Prepare condition
	 *
	 * @return array
	 */
	public function prepare_condition(): array {
		$sql        = '';
		$vars       = array();
		$conditions = $this->get_conditions();
		$where      = $conditions['where'];
		$where_like = $conditions['where_like'];
		$order      = $conditions['order'];

		foreach ( $where as $id => $value ) {
			if ( empty( $sql ) ) {
				$sql .= " WHERE {$value[0]}{$value[3]}{$value[2]}";
			} else {
				$sql .= " {$this->where_relation} {$value[0]}{$value[3]}{$value[2]}";
			}
			$vars[] = trim( $value[1] );
		}

		foreach ( $where_like as $id => $value ) {
			if ( empty( $sql ) ) {
				$sql .= " WHERE {$value[0]} LIKE {$value[2]}";
			} else {
				$sql .= " {$this->where_relation} {$value[0]} LIKE {$value[2]}";
			}
			$vars[] = trim( $value[1] );
		}

		foreach ( $order as $id => $value ) {
			if ( ! is_numeric( strpos( $sql, 'ORDER BY' ) ) ) {
				$sql .= " ORDER BY {$value['column']} {$value['type']}";
			} else {
				$sql .= ", {$value['column']} {$value['type']}";
			}
		}

		if ( ! is_null( $this->limit ) ) {
			$sql   .= ' LIMIT %d';
			$vars[] = $this->limit;
		}

		if ( ! is_null( $this->limit ) && ! is_null( $this->offset ) ) {
			$sql   .= ' OFFSET %d';
			$vars[] = $this->offset;
		}

		return array(
			'sql'  => $sql,
			'vars' => $vars,
		);
	}
}
