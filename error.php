<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'site';
    private $username = 'tejoshrana';
    private $password = '40j@jtra';
    private $conn;

    // Method to establish a new database connection
    public function NewConnection() {
        $this->conn = null;

        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            if ($this->conn->connect_errno) {
                throw new Exception("Failed to connect to MySQL: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            echo "Connection error: " . $e->getMessage();
        }

        return $this->conn;
    }

    // Method to close the database connection
    public function CloseConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    // Method to execute a query and return a single row as an associative array
    public function getArray($sql, $params = []) {
        $conn = $this->NewConnection();
        if (!$conn) {
            throw new Exception("Failed to establish a database connection.");
        }

        $stmt = $conn->prepare($sql);
        if ($params) {
            $stmt->bind_param(...$this->prepareParams($params));
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Method to execute a query and return all rows as an associative array
    public function getAll($sql, $params = []) {
        $conn = $this->NewConnection();
        if (!$conn) {
            throw new Exception("Failed to establish a database connection.");
        }

        $stmt = $conn->prepare($sql);
        if ($params) {
            $stmt->bind_param(...$this->prepareParams($params));
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Helper method to prepare parameters for binding
    private function prepareParams($params) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_double($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        return array_merge([$types], $params);
    }
}
?>
