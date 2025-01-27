<?php
    class User {
        protected $pdo;

        function __construct($pdo)
        {
            $this->pdo = $pdo;
        }

        public function sanitizeInput ($value)
        {
            $value = htmlspecialchars($value);
            $value = trim($value);
            $value = stripslashes($value);
            return $value;
        }

        public function isEmailUnique ($email)
        {
            $stmt = $this->pdo->prepare("SELECT email FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->rowCount();
            return $count > 0;
        }

        public function create ($table, $fields=[])
        {
            $columns = implode(',', array_keys($fields));
            $values = ":" . implode(', :', array_keys($fields));

            $sql = "INSERT INTO $table (" . $columns . ") VALUES($values)";
            if($stmt = $this->pdo->prepare($sql)) 
            {
                foreach($fields as $key => $value) 
                {
                    $stmt->bindValue(":" . $key, $value);
                }

                $stmt->execute();
                return $this->pdo->lastInsertId();
            }
        }
    }
?>