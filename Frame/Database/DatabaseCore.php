<?php

namespace Frame\Database;

use PDO;
use PDOStatement;

class DatabaseCore
{
    protected PDO $pdo;
    protected PDOStatement $pdoStatement;
    private static array $dsn = array(
        'mysql' => 'mysql:host=%s;port=%d;dbname=%s',
        'pgsql' => 'pgsql:host=%s;port=%d;dbname=%s',
        'dblib' => 'dblib:host=%s:%d;dbname=%s',
        'mssql' => 'sqlsrv:Server=%s,%d;Database=%s',
        'cubrid' => 'cubrid:host=%s;port=%d;dbname=%s'
    );
    private string $logLocation = __DIR__ . '/logs';
    private bool $logErrors = true;

    public function connect($hostname, $username, $password, $dbname, $type = 'mysql', $port = 3306, array $options = []): self
    {
        $dsn = sprintf(self::$dsn[$type], $hostname, $port, $dbname);
        if ($type == 'mysql') {
            $options[PDO::MYSQL_ATTR_USE_BUFFERED_QUERY] = true;
            $options[PDO::ATTR_EMULATE_PREPARES] = true;
        }
        try {
            $this->pdo = new PDO($dsn, $username, $password, $options);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            print "Error: " . $e->getMessage();
        }
        return $this;
    }

    public function setLogLocation($location): self
    {
        $this->logLocation = $location;
        if (!is_dir($this->logLocation)) {
            mkdir($this->logLocation, 0777, true);
            file_put_contents($this->logLocation . '/.htaccess', 'order deny,allow' . PHP_EOL . 'deny from all');
        }
        return $this;
    }

    public function setLogErrors(bool $logErrors): self
    {
        $this->logErrors = $logErrors;
        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function error($message): void
    {
        if ($this->logErrors) {
            $logString = date('d/m/Y H:i:s') . PHP_EOL;
            $logString .= "Error: $message" . PHP_EOL;
            $logString .= '----------------------' . PHP_EOL . PHP_EOL;
            $file = $this->logLocation . '/sql.txt';
            file_put_contents($file, $logString, FILE_APPEND);
        }
        throw new \Exception($message);
    }
}