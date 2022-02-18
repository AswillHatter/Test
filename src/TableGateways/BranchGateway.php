<?php

namespace Src\TableGateways;

class BranchGateway
{

    private $db = null;

    /**
     * @param $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        $statement = "
            SELECT 
                id, name, parent_id
            FROM
                branch;
        ";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * @param $id
     * @return array
     */
    public function find($id): array
    {
        $statement = "
            SELECT 
                id, name, parent_id
            FROM
                branch
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int)$id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * @param array $input
     * @return void
     */
    public function insert(array $input)
    {
        $statement = "
            INSERT INTO branch 
                (name, parent_id)
            VALUES
                (:name, :parent_id);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'name' => $input['name'],
                'parent_id' => $input['parent_id'],
            ));
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * @param $id
     * @param array $input
     * @return void
     */
    public function update($id, array $input)
    {
        $statement = "
            UPDATE branch
            SET 
                name = :name,
                parent_id = :parent_id
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(
                array(
                    'id' => (int)$id,
                    'name' => $input['name'],
                    'parent_id' => $input['parent_id'],
                ));
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * @param $id
     * @return void
     */
    public function delete($id)
    {
        $statement = "
            DELETE FROM branch
            WHERE id = :id;
            DELETE FROM branch
            WHERE parent_id = :id;
        ";

        try {
            $newarray = array('id' => (int)$id);
            $statement = $this->db->prepare($statement);
            $statement->execute($newarray);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}