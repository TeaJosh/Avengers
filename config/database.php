<?php
/**
 * Database Class for Blog Application
 * 
 * Last updated: 2025-04-30 03:56:07 UTC
 * Updated by: Tejosh Rana
 * 
 * This class provides a data access layer for the blog application.
 * It handles database connections and CRUD operations for all entities.
 */
class Database {
    // Database connection attributes
    private $user;
    private $password;
    private $db;
    private $server;
    private $connection;
    
    // Entity validation rules
    private $entityRules;
    
    /**
     * Default constructor that uses default credentials
     */
    public function __construct($user = "bloguser", $password = "p@ssword", $db = "tranablog", $server = "localhost") {
        $this->user = $user;
        $this->password = $password;
        $this->db = $db;
        $this->server = $server;
        
        // Test connection immediately
        $connection = $this->connect();
        if ($connection === null) {
            throw new PDOException("Failed to connect to database. Please check credentials and server status.");
        }
        
        // Updated entityRules to match schema
        $this->entityRules = [
            'roles' => [
                'pk' => 'id',
                'unique' => ['name'],
                'required' => ['name'],
                'optional' => ['description'],
                'foreignKeys' => [],
                'timestamps' => false
            ],
            'users' => [
                'pk' => 'id',
                'unique' => ['username'],
                'required' => ['username', 'password', 'role_id'],
                'optional' => [],
                'foreignKeys' => ['role_id' => 'roles'],
                'timestamps' => ['created_at']
            ],
            'users_info' => [
                'pk' => 'user_id',
                'unique' => ['email'],
                'required' => ['user_id', 'email'],
                'optional' => ['fname', 'lname', 'address', 'phone', 'occupation', 'bio', 'pfp'],
                'foreignKeys' => ['user_id' => 'users'],
                'timestamps' => false,
                'cascade' => true
            ],
            'topics' => [
                'pk' => 'id',
                'unique' => ['name'],
                'required' => ['name', 'created_by'],
                'optional' => ['description'],
                'foreignKeys' => ['created_by' => 'users'],
                'timestamps' => ['created_at']
            ],
            'posts' => [
                'pk' => 'id',
                'unique' => [],
                'required' => ['topic_id', 'created_by', 'title', 'content'],
                'optional' => [],
                'foreignKeys' => ['topic_id' => 'topics', 'created_by' => 'users'],
                'timestamps' => ['created_at', 'updated_at'],
                'validation' => [
                    'title' => function($value) { return strlen($value) <= 50; }
                ],
                'cascade' => true
            ],
            'comments' => [
                'pk' => 'id',
                'unique' => [],
                'required' => ['post_id', 'created_by', 'content'],
                'optional' => [],
                'foreignKeys' => ['post_id' => 'posts', 'created_by' => 'users'],
                'timestamps' => ['created_at', 'updated_at'],
                'cascade' => true
            ],
            'rankings' => [
                'pk' => 'id',
                'unique' => [['post_id', 'created_by']],
                'required' => ['post_id', 'created_by', 'rating'],
                'optional' => [],
                'foreignKeys' => ['post_id' => 'posts', 'created_by' => 'users'],
                'timestamps' => ['created_at'],
                'customValidation' => ['rating' => function($value) { return $value >= 1 && $value <= 5; }],
                'cascade' => true
            ]
        ];
    }

    /**
     * Connect to the database
     * 
     * @return PDO|null The database connection or null if connection fails
     */
    public function connect() {
        try {
            $dsn = "mysql:host={$this->server};dbname={$this->db};charset=utf8mb4";
            $this->connection = new PDO($dsn, $this->user, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $this->connection;
        } catch (PDOException $e) {
            error_log("Connection failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Close the database connection
     */
    public function close() {
        $this->connection = null;
    }

    /**
     * Execute a query with no result
     */
    public function Query($sql, $params = []) {
        try {
            if ($this->connection === null) {
                $this->connect();
                if ($this->connection === null) {
                    error_log("Database connection is null in Query");
                    return false;
                }
            }
            
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Query failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute a query and return all results
     */
    public function QueryAll($sql, $params = []) {
        try {
            if ($this->connection === null) {
                $this->connect();
                if ($this->connection === null) {
                    error_log("Database connection is null in QueryAll");
                    return null;
                }
            }
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("QueryAll failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Execute a query and return an associative array of results
     */
    public function QueryArray($sql, $params = []) {
        try {
            if ($this->connection === null) {
                $this->connect();
                if ($this->connection === null) {
                    error_log("Database connection is null in QueryArray");
                    return null;
                }
            }
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("QueryArray failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generic method to get record(s) from any entity
     */
    public function Get($table, $id = null, $column = null, $value = null) {
        if (!array_key_exists($table, $this->entityRules)) {
            error_log("Unknown table: $table");
            return null;
        }
        
        $pk = $this->entityRules[$table]['pk'];
        
        if ($id !== null) {
            return $this->QueryArray("SELECT * FROM $table WHERE $pk = ?", [$id]);
        }
        else if ($column !== null && $value !== null) {
            return $this->QueryArray("SELECT * FROM $table WHERE $column = ?", [$value]);
        }
        else {
            return $this->QueryArray("SELECT * FROM $table");
        }
    }

    /**
     * Generic method to add a record to any entity
     */
    public function Add($table, $data) {
        if (!array_key_exists($table, $this->entityRules)) {
            error_log("Unknown table: $table");
            return false;
        }
        
        $rules = $this->entityRules[$table];
        
        // Required fields check
        foreach ($rules['required'] as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                error_log("Missing required field: $field for $table");
                return false;
            }
        }
        
        // Add timestamps if required
        if (isset($rules['timestamps'])) {
            $now = date('Y-m-d H:i:s');
            if (in_array('created_at', $rules['timestamps'])) {
                $data['created_at'] = $now;
            }
            if (in_array('updated_at', $rules['timestamps'])) {
                $data['updated_at'] = $now;
            }
        }
        
        // Validation checks
        if ($table === 'posts' && isset($data['title']) && strlen($data['title']) > 50) {
            error_log("Post title exceeds maximum length of 50 characters");
            return false;
        }
        
        // Unique constraints check
        foreach ($rules['unique'] as $constraint) {
            if (is_array($constraint)) {
                // Compound unique constraint
                $conditions = [];
                $values = [];
                foreach ($constraint as $field) {
                    if (isset($data[$field])) {
                        $conditions[] = "$field = ?";
                        $values[] = $data[$field];
                    }
                }
                if (!empty($conditions)) {
                    $sql = "SELECT COUNT(*) as count FROM $table WHERE " . implode(" AND ", $conditions);
                    $result = $this->QueryArray($sql, $values);
                    if ($result !== null && isset($result[0]['count']) && $result[0]['count'] > 0) {
                        error_log("Compound unique constraint violation in $table");
                        return false;
                    }
                }
            } else {
                // Single field unique constraint
                if (isset($data[$constraint])) {
                    $existing = $this->Get($table, null, $constraint, $data[$constraint]);
                    if (!empty($existing)) {
                        error_log("Value for $constraint already exists in $table");
                        return false;
                    }
                }
            }
        }
        
        // Foreign keys check
        foreach ($rules['foreignKeys'] as $field => $foreignTable) {
            if (isset($data[$field])) {
                $foreignRecord = $this->Get($foreignTable, $data[$field]);
                if (empty($foreignRecord)) {
                    error_log("Foreign key constraint failed: $field in $table references $foreignTable");
                    return false;
                }
            }
        }
        
        // Password hashing for users table
        if ($table === 'users' && isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        // Custom validations
        if (isset($rules['customValidation'])) {
            foreach ($rules['customValidation'] as $field => $validationFn) {
                if (isset($data[$field]) && !$validationFn($data[$field])) {
                    error_log("Custom validation failed for $field in $table");
                    return false;
                }
            }
        }
        
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $success = $this->Query($sql, array_values($data));
        
        return $success ? $this->connection->lastInsertId() : false;
    }

    /**
     * Generic method to update a record in any entity
     */
    public function Update($table, $id, $data) {
        if (!array_key_exists($table, $this->entityRules)) {
            error_log("Unknown table: $table");
            return false;
        }
        
        $rules = $this->entityRules[$table];
        $pk = $rules['pk'];
        
        // Check if record exists
        $record = $this->Get($table, $id);
        if (empty($record)) {
            error_log("Record not found in $table with $pk = $id");
            return false;
        }
        
        // Update timestamp if required
        if (isset($rules['timestamps']) && in_array('updated_at', $rules['timestamps'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        // Validation checks
        if ($table === 'posts' && isset($data['title']) && strlen($data['title']) > 50) {
            error_log("Post title exceeds maximum length of 50 characters");
            return false;
        }
        
        // Unique constraints check
        foreach ($rules['unique'] as $constraint) {
            if (is_array($constraint)) {
                // Compound unique constraint
                $conditions = [];
                $values = [];
                foreach ($constraint as $field) {
                    if (isset($data[$field])) {
                        $conditions[] = "$field = ?";
                        $values[] = $data[$field];
                    }
                }
                if (!empty($conditions)) {
                    $sql = "SELECT COUNT(*) as count FROM $table WHERE " . implode(" AND ", $conditions) . " AND $pk != ?";
                    $values[] = $id;
                    $result = $this->QueryArray($sql, $values);
                    if ($result[0]['count'] > 0) {
                        error_log("Compound unique constraint violation in $table");
                        return false;
                    }
                }
            } else {
                // Single field unique constraint
                if (isset($data[$constraint])) {
                    $existing = $this->Get($table, null, $constraint, $data[$constraint]);
                    if (!empty($existing) && $existing[0][$pk] != $id) {
                        error_log("Value for $constraint already exists in $table");
                        return false;
                    }
                }
            }
        }
        
        // Foreign keys check
        foreach ($rules['foreignKeys'] as $field => $foreignTable) {
            if (isset($data[$field])) {
                $foreignRecord = $this->Get($foreignTable, $data[$field]);
                if (empty($foreignRecord)) {
                    error_log("Foreign key constraint failed: $field in $table references $foreignTable");
                    return false;
                }
            }
        }
        
        // Password hashing for users table
        if ($table === 'users' && isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        // Custom validations
        if (isset($rules['customValidation'])) {
            foreach ($rules['customValidation'] as $field => $validationFn) {
                if (isset($data[$field]) && !$validationFn($data[$field])) {
                    error_log("Custom validation failed for $field in $table");
                    return false;
                }
            }
        }
        
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        
        $sql = "UPDATE $table SET " . implode(", ", $fields) . " WHERE $pk = ?";
        return $this->Query($sql, $values);
    }

    /**
     * Generic method to delete a record from any entity
     */
    public function Delete($table, $id) {
        if (!array_key_exists($table, $this->entityRules)) {
            error_log("Unknown table: $table");
            return false;
        }
        
        $rules = $this->entityRules[$table];
        $pk = $rules['pk'];
        
        // Check if record exists
        $record = $this->Get($table, $id);
        if (empty($record)) {
            error_log("Record not found in $table with $pk = $id");
            return false;
        }
        
        // Special handling for roles (prevent deletion if in use)
        if ($table === 'roles') {
            $usersWithRole = $this->QueryArray("SELECT COUNT(*) as count FROM users WHERE role_id = ?", [$id]);
            if ($usersWithRole[0]['count'] > 0) {
                error_log("Cannot delete role: it is in use");
                return false;
            }
        }
        
        // For tables with cascade delete, we don't need additional checks
        if (!isset($rules['cascade']) || !$rules['cascade']) {
            // Check for dependent records in other tables
            foreach ($this->entityRules as $relatedTable => $relatedRules) {
                if ($relatedTable === $table) continue;
                
                foreach ($relatedRules['foreignKeys'] as $fkField => $fkTable) {
                    if ($fkTable === $table) {
                        $dependentRecords = $this->QueryArray(
                            "SELECT COUNT(*) as count FROM $relatedTable WHERE $fkField = ?",
                            [$id]
                        );
                        if ($dependentRecords[0]['count'] > 0) {
                            error_log("Cannot delete $table: records exist in $relatedTable");
                            return false;
                        }
                    }
                }
            }
        }
        
        return $this->Query("DELETE FROM $table WHERE $pk = ?", [$id]);
    }

    /**
     * Roles methods
     */
    public function GetRole($id = null, $column = null, $value = null) {
        return $this->Get('roles', $id, $column, $value);
    }
    
    public function AddRole($name, $description = null) {
        $data = ['name' => $name];
        if ($description !== null) {
            $data['description'] = $description;
        }
        return $this->Add('roles', $data);
    }
    
    public function UpdateRole($id, $data) {
        return $this->Update('roles', $id, $data);
    }
    
    public function DeleteRole($id) {
        return $this->Delete('roles', $id);
    }
    
    /**
     * Users methods
     */
    public function GetUser($id = null, $column = null, $value = null) {
        return $this->Get('users', $id, $column, $value);
    }
    
    public function AddUser($username, $password, $role_id) {
        return $this->Add('users', [
            'username' => $username,
            'password' => $password,
            'role_id' => $role_id
        ]);
    }
    
    public function UpdateUser($id, $data) {
        return $this->Update('users', $id, $data);
    }
    
    public function DeleteUser($id) {
        return $this->Delete('users', $id);
    }
    
    /**
     * Users_info methods
     */
    public function GetUserInfo($user_id = null, $column = null, $value = null) {
        return $this->Get('users_info', $user_id, $column, $value);
    }
    
    public function AddUserInfo($user_id, $data) {
        if (!isset($data['email'])) {
            error_log("Email is required for users_info");
            return false;
        }
        $data['user_id'] = $user_id;
        return $this->Add('users_info', $data);
    }
    
    public function UpdateUserInfo($user_id, $data) {
        return $this->Update('users_info', $user_id, $data);
    }
    
    public function DeleteUserInfo($user_id) {
        return $this->Delete('users_info', $user_id);
    }
    
    /**
     * Topics methods
     */
    public function GetTopic($id = null, $column = null, $value = null) {
        return $this->Get('topics', $id, $column, $value);
    }
    
    public function AddTopic($name, $created_by, $description = null) {
        $data = [
            'name' => $name,
            'created_by' => $created_by
        ];
        if ($description !== null) {
            $data['description'] = $description;
        }
        return $this->Add('topics', $data);
    }
    
    public function UpdateTopic($id, $data) {
        return $this->Update('topics', $id, $data);
    }
    
    public function DeleteTopic($id) {
        return $this->Delete('topics', $id);
    }
    
    /**
     * Posts methods
     */
    public function GetPost($id = null, $column = null, $value = null) {
        return $this->Get('posts', $id, $column, $value);
    }
    
    public function AddPost($topic_id, $created_by, $title, $content) {
        return $this->Add('posts', [
            'topic_id' => $topic_id,
            'created_by' => $created_by,
            'title' => $title,
            'content' => $content
        ]);
    }
    
    public function UpdatePost($id, $data) {
        return $this->Update('posts', $id, $data);
    }
    
    public function DeletePost($id) {
        return $this->Delete('posts', $id);
    }
    
    /**
     * Comments methods
     */
    public function GetComment($id = null, $column = null, $value = null) {
        return $this->Get('comments', $id, $column, $value);
    }
    
    public function AddComment($post_id, $created_by, $content) {
        return $this->Add('comments', [
            'post_id' => $post_id,
            'created_by' => $created_by,
            'content' => $content
        ]);
    }
    
    public function UpdateComment($id, $data) {
        return $this->Update('comments', $id, $data);
    }
    
    public function DeleteComment($id) {
        return $this->Delete('comments', $id);
    }
    
    /**
     * Rankings methods
     */
    public function GetRanking($id = null, $column = null, $value = null) {
        return $this->Get('rankings', $id, $column, $value);
    }
    
    public function AddRanking($post_id, $created_by, $rating) {
        // Check for existing rating (unique constraint)
        $existingRating = $this->QueryArray(
            "SELECT * FROM rankings WHERE post_id = ? AND created_by = ?", 
            [$post_id, $created_by]
        );
        
        if (!empty($existingRating)) {
            return $this->UpdateRanking($existingRating[0]['id'], ['rating' => $rating]);
        }
        
        return $this->Add('rankings', [
            'post_id' => $post_id,
            'created_by' => $created_by,
            'rating' => $rating
        ]);
    }
    
    public function UpdateRanking($id, $data) {
        return $this->Update('rankings', $id, $data);
    }
    
    public function DeleteRanking($id) {
        return $this->Delete('rankings', $id);
    }
}
?>
