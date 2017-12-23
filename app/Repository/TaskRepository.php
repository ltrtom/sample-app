<?php

namespace App\Repository;

use PDO;

class TaskRepository
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
     * @param int $userId
     * @return array
     */
    public function findAll(int $userId)
    {
        $sql = 'SELECT id, description, creation_date, status FROM task WHERE user_id = :user_id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $userId
     * @param int $id
     * @return array|null
     */
    public function findOne(int $userId, int $id)
    {
        $sql = 'SELECT id, description, creation_date, status FROM task WHERE user_id = :user_id AND id = :id';
        $stmt  = $this->connection->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    /**
     * Remove a user from the database
     * @param int $userId
     * @param int $id $
     */
    public function delete(int $userId, int $id)
    {
        $stmt = $this->connection->prepare('DELETE FROM task WHERE user_id = :user_id AND id = :id');

        $stmt->execute(['user_id' => $userId, 'id' => $id]);
    }

    /**
     * Remove all task from a given user
     * @param int $userId
     */
    public function deleteFromUser(int $userId)
    {
        $stmt = $this->connection->prepare('DELETE FROM task WHERE user_id = :user_id');

        $stmt->execute(['user_id' => $userId]);
    }

    /**
     * Persist a task in database
     *
     * @param int $userId
     * @param string $description
     * @param bool $status
     * @return int the id of the inserted task
     */
    public function insertTask(int $userId, string $description, bool $status)
    {
        $sql = 'INSERT INTO task(user_id, description, creation_date, status) 
                VALUES (:user_id, :description, NOW(), :status)';

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'description' => $description,
            'status' => $status,
        ]);

        return $this->connection->lastInsertId();
    }

}