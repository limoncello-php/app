<?php namespace App\Database\Seeds;

use App\Database\Models\Board;
use App\Database\Models\Comment;
use App\Database\Models\Model;
use App\Database\Models\Post;
use App\Database\Models\Role;
use App\Database\Models\User;
use Closure;
use Faker\Factory;
use PDO;

/**
 * @package App
 */
class SeedsRunner
{
    /**
     * @param PDO $pdo
     *
     * @return void
     */
    public function run(PDO $pdo)
    {
        $faker = Factory::create();
        $faker->seed(1234);

        $this->seedTable($pdo, 10, Board::TABLE_NAME, function () use ($faker) {
            return [
                Board::FIELD_TITLE => 'Board ' . $faker->text(20),
            ];
        });

        $this->seedTable($pdo, 5, Role::TABLE_NAME, function () use ($faker) {
            return [
                Role::FIELD_NAME => 'Role ' . $faker->text(20),
            ];
        });

        $allRoles = $this->readAll($pdo, Role::TABLE_NAME, Role::class);
        $this->seedTable($pdo, 10, User::TABLE_NAME, function () use ($faker, $allRoles) {
            return [
                User::FIELD_ID_ROLE       => $faker->randomElement($allRoles)->{Role::FIELD_ID},
                User::FIELD_TITLE         => $faker->title,
                User::FIELD_FIRST_NAME    => $faker->firstName,
                User::FIELD_LAST_NAME     => $faker->lastName,
                User::FIELD_LANGUAGE      => $faker->languageCode,
                User::FIELD_EMAIL         => $faker->email,
                User::FIELD_PASSWORD_HASH => 'some_hash',
                User::FIELD_API_TOKEN     => 'some_token',
            ];
        });

        $allBoards = $this->readAll($pdo, Board::TABLE_NAME, Board::class);
        $allUsers  = $this->readAll($pdo, User::TABLE_NAME, User::class);
        $this->seedTable($pdo, 100, Post::TABLE_NAME, function () use ($faker, $allBoards, $allUsers) {
            return [
                Post::FIELD_ID_BOARD => $faker->randomElement($allBoards)->{Board::FIELD_ID},
                Post::FIELD_ID_USER  => $faker->randomElement($allUsers)->{User::FIELD_ID},
                Post::FIELD_TITLE    => $faker->text(50),
                Post::FIELD_TEXT     => $faker->text(),
            ];
        });

        $allPosts = $this->readAll($pdo, Post::TABLE_NAME, Post::class);
        $this->seedTable($pdo, 400, Comment::TABLE_NAME, function () use ($faker, $allPosts, $allUsers) {
            return [
                Comment::FIELD_ID_POST => $faker->randomElement($allPosts)->{Post::FIELD_ID},
                Comment::FIELD_ID_USER => $faker->randomElement($allUsers)->{User::FIELD_ID},
                Comment::FIELD_TEXT    => $faker->text(),
            ];
        });
    }

    /**
     * @param PDO     $pdo
     * @param int     $records
     * @param string  $tableName
     * @param Closure $fieldsClosure
     */
    private function seedTable(PDO $pdo, $records, $tableName, Closure $fieldsClosure)
    {
        for ($i = 0; $i !== (int)$records; $i++) {
            $fields    = $fieldsClosure();
            $fields    = array_merge($fields, [Model::FIELD_CREATED_AT => date('Y-m-d H:i:s')]);
            $columns   = implode(', ', array_keys($fields));
            $values    = implode('\', \'', array_values($fields));
            $statement = "INSERT INTO $tableName ($columns) VALUES ('$values')";
            $result    = $pdo->exec($statement);
            assert($result !== false, 'Statement execution failed');
        }
    }

    /**
     * @param PDO    $pdo
     * @param string $tableName
     * @param string $className
     *
     * @return array
     */
    protected function readAll(PDO $pdo, $tableName, $className)
    {
        $result = $pdo->query("SELECT * FROM $tableName")->fetchAll(PDO::FETCH_CLASS, $className);

        return $result;
    }
}
