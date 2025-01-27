<?php
class DB {
    private static function connect()
    {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=facebook;charset=utf8mb4', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    public static function query ($query, $params = [])
    {
        $stmt = self::connect()->prepare($query);
        $stmt->execute($params);
        
        if (explode(' ', $query)[0] === 'SELECT')
        {
            $data = $stmt->fetchAll();
            return $data;
        }
    } 
}

?>
