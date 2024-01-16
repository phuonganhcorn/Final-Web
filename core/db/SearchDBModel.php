<?php

namespace app\core\db;

use app\core\Model;
use app\models\Subject;
use PDO;

abstract class SearchDbModel extends Model
{
    // Table want to be saved
    abstract public function tableName(): string;
    // List attribute save to Db
    abstract public function attributes(): array;

    abstract public function primaryKey(): string;

    public function getClassSearch()
    {
        return static::class;
    }

    public function search($searchKey, $searchValue, $withRelations = [])
    {
        $tableName = static::tableName();
        $sql = $this->buildSqlQuery($where, $method);
        $params = [];

        if (!empty($withRelations)) {
            $sql = $this->applyJoinConditions($sql, $withRelations);
        }

        $statement = self::prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll(PDO::FETCH_CLASS, static::getClassSearch());
    }

    public function findAll(array $where = [], string $method = '=', array $columns = ['*'], $withRelations = [])
    {
        $tableName = static::tableName();
        $columnSQL = implode(', ', $columns);
        $sql = $this->buildSqlQuery($where, $method);
        $params = [];

        if (!empty($withRelations)) {
            $sql = $this->applyJoinConditions($sql, $withRelations);
        }

        $statement = self::prepare("SELECT $columnSQL FROM $tableName WHERE $sql");
        $this->bindValuesToStatement($statement, $where);

        if (empty($where)) {
            $statement = self::prepare("SELECT $columnSQL FROM $tableName");
        }

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    public function findOne(array $where, $withRelations = [])
    {
        $tableName = static::tableName();
        $sql = $this->buildSqlQuery($where);
        $params = [];

        if (!empty($withRelations)) {
            $sql = $this->applyJoinConditions($sql, $withRelations);
        }

        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        $this->bindValuesToStatement($statement, $where);
        $statement->execute();

        return $statement->fetchObject(static::class);
    }

    private function applyJoinConditions($sql, $withRelations)
    {
        foreach ($withRelations as $relation) {
            $sql .= " JOIN $relation[0] ON $relation[1] = $relation[2]";
        }

        return $sql;
    }

    private function buildSqlQuery(array $where, string $method = '=')
    {
        $conditions = [];

        foreach ($where as $attribute => $value) {
            if ($this->isValidSearchValue($value)) {
                $conditions[] = $this->buildSearchCondition($attribute, $value, $method);
            }
        }

        return implode(' AND ', $conditions);
    }

    private function buildSearchCondition($attribute, $value, $method)
    {
        if (is_array($value)) {
            return $this->buildMultipleColumnsCondition($attribute, $value, 'LIKE');
        } else {
            return "$attribute $method :$attribute";
        }
    }

    private function buildMultipleColumnsCondition($attribute, $values, $method)
    {
        $likeConditions = [];

        foreach ($values as $col => $colValue) {
            $likeConditions[] = "$col $method :$col";
        }

        return '(' . implode(' OR ', $likeConditions) . ')';
    }
}
