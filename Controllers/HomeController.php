<?php

namespace app\Controllers;

use app\core\Controller;
use app\core\Request;
use PDO;

class HomeController extends Controller
{
    /**
     * @return array|false|string|string[]
     */
    public function home(Request $request)
    {
        $user = 'root';
        $pass = 'password';
        $server = 'localhost';

        $dbh = new PDO("mysql:host=$server", $user, $pass);
        $dbs = $dbh->query('SHOW DATABASES')->fetchAll();

        $query = "SHOW TABLES";
        $db_tables = [];

        foreach ($dbs as $index => $db) {
            $pdo = new PDO("mysql:dbname=$db[0];host=$server", $user, $pass);
            $statement = $pdo->prepare($query);
            $statement->execute();
            $tables = $statement->fetchAll(PDO::FETCH_NUM);

            $db_tables[$index]['name'] = $db[0];

            foreach ($tables as $table) {
                $db_tables[$index]['tables'][] = $table;
            }
        }

        $dbName = $request->getBody()['dbName'];
        $tableName = $request->getBody()['tableName'];

        $pdo = new PDO("mysql:dbname=$dbName;host=$server", $user, $pass);

        $query = "SELECT * FROM $tableName";
        $statement = $pdo->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (!count($result)) {
            $query = "DESC $tableName";
            $statement = $pdo->prepare($query);
            $statement->execute();
            $fields = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($fields as $field) {
                $result['fields'][] = $field['Field'];
            }
        }

        $params = [
            'dbs_tables' => $db_tables,
            'table_data' => $result,
        ];

        return $this->view('home', $params);
    }

    /**
     * @param Request $request
     *
     * @return false|string
     */
    public function show_table(Request $request)
    {
        $user = 'root';
        $pass = 'password';
        $server = 'localhost';

        $data = $request->getBody();
        $data = json_decode(key((array)$data));

        $dbName = $data->dbName;
        $tableName = $data->tableName;

        $pdo = new PDO("mysql:dbname=$dbName;host=$server", $user, $pass);

        $query = "SELECT * FROM $tableName";
        $statement = $pdo->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (!count($result)) {
            $query = "DESC $tableName";
            $statement = $pdo->prepare($query);
            $statement->execute();
            $fields = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($fields as $field) {
                $result['fields'][] = $field['Field'];
            }
        }

        return json_encode($result);
    }

    /**
     * @param Request $request
     * @return false|string
     */
    public function destroy(Request $request)
    {
        $user = 'root';
        $pass = 'password';
        $server = 'localhost';

        $dbName = $request->getBody()['dbName'];
        $tableName = $request->getBody()['tableName'];
        $id = $request->getBody()['id'];

        $pdo = new PDO("mysql:dbname=$dbName;host=$server", $user, $pass);

        $sql = "DELETE FROM $tableName WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();


        //select after deleting item, to show other items
        $query = "SELECT * FROM $tableName";
        $statement = $pdo->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    /**
     * @param Request $request
     * @return false|string
     */
    public function edit(Request $request)
    {
        $user = 'root';
        $pass = 'password';
        $server = 'localhost';

        $dbName = $request->getBody()['dbName'];
        $tableName = $request->getBody()['tableName'];
        $id = $request->getBody()['id'];

        $pdo = new PDO("mysql:dbname=$dbName;host=$server", $user, $pass);

        $query = "SELECT * FROM $tableName WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

        return json_encode($result);
    }

    /**
     * @param Request $request
     * @return false|string
     */
    public function update(Request $request)
    {
        $user = 'root';
        $pass = 'password';
        $server = 'localhost';

        $data = $request->getBody();
        $data = json_decode(key((array)$data));
        $data->values = (array)$data->values;

        $dbName = $data->dbName;
        $tableName = $data->tableName;
        $id = $data->id;

        $pdo = new PDO("mysql:dbname=$dbName;host=$server", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $sql = "UPDATE $tableName SET ";
        $lastKey = array_key_last($data->values);

        foreach ($data->values as $key => $value) {
            $sql .= "$key=:$key";

            if ($lastKey !== $key) {
                $sql .= ", ";
            }
        }

        try {
            $data->values['where_id'] = $id;
            $sql .= " WHERE id=:where_id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($data->values);

            //select data after update
            $query = "SELECT * FROM $tableName";
            $statement = $pdo->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            return json_encode($result);

        } catch (\Exception $e) {

            var_dump($e);
            die;
        }
    }

    /**
     * @param Request $request
     * @return false|string
     */
    public function create_item(Request $request)
    {
        $user = 'root';
        $pass = 'password';
        $server = 'localhost';
        $dbName = $request->getBody()['dbName'];
        $tableName = $request->getBody()['tableName'];

        $pdo = new PDO("mysql:dbname=$dbName;host=$server", $user, $pass);

        $query = "DESC $tableName";
        $statement = $pdo->prepare($query);
        $statement->execute();
        $fields = $statement->fetchAll(PDO::FETCH_ASSOC);

        $result = [];

        foreach ($fields as $field) {
            $result[] = $field['Field'];
        }

        return json_encode($result);
    }

    public function store_item(Request $request)
    {
        $user = 'root';
        $pass = 'password';
        $server = 'localhost';

        $data = json_decode(key((array)$request->getBody()));
        $data->values = (array)$data->values;

        $dbName = $data->dbName;
        $tableName = $data->tableName;

        $pdo = new PDO("mysql:dbname=$dbName;host=$server", $user, $pass);

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $sql = "INSERT INTO $tableName (";
        $lastKey = array_key_last($data->values);

        foreach ($data->values as $key => $value) {
            $sql .= "$key";
            if ($value == null) {
                $values .= "NULL";
            } else {
                $values .= "'$value'";
            }

            if ($lastKey !== $key) {
                $sql .= ", ";
                $values .= ", ";
            } else {
                $sql .= ") VALUES ($values)";
            }
        }

        try {
            $statement = $pdo->prepare($sql);
            $statement->execute();

            //select data after insert
            $query = "SELECT * FROM $tableName";
            $statement = $pdo->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            return json_encode($result);
        } catch (\Exception $e) {
            var_dump($e);
            die;
        }
    }

    /**
     * @param Request $request
     */
    public function store_db(Request $request)
    {
        $user = 'root';
        $pass = 'password';
        $server = 'localhost';
        $db_name = $request->getBody()['dbName'];
        $dbh = new PDO("mysql:host=$server", $user, $pass);

        $dbh->exec("CREATE DATABASE `$db_name`");
    }

    public function view_db()
    {
        $content = "";
    }


}
