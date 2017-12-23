<?php

namespace App\Repository;

use PDO;

class UserRepository
{
    /**
     * @var PDO
     */
    protected $connection;

    /**
     * UserRepository constructor.
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return array
     */
    public function findAll()
    {
        $sql = 'SELECT id, name, email FROM `user` ';
        $stmt = $this->connection->query($sql, PDO::FETCH_ASSOC);

        return $stmt->fetchAll();
    }

    /**
     * @param int $id
     * @return array|null
     */
    public function findOne(int $id)
    {
        $sql = 'SELECT id, name, email FROM `user` WHERE id = :id';
        $stmt  = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    /**
     * Remove a user from the database
     * @param int $id$
     */
    public function delete(int $id)
    {
        $stmt = $this->connection->prepare('DELETE FROM `user` WHERE id = :id');

        $stmt->execute(['id' => $id]);
    }

    /**
     * Persist a user into database
     * @param $email
     * @param $name
     * @return int the id of the inserted user
     */
    public function insertUser($email, $name)
    {
        $stmt = $this->connection->prepare('INSERT INTO `user`(email, name) VALUES (:email, :name)');
        $stmt->execute(['email' => $email, 'name' => $name]);

        return $this->connection->lastInsertId();
    }

}