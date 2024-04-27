
# Database
Prepare placeholders in query and returns it secure for execution  
## Placeholders:
`?s` string escaped through PDO->quote  
`?i` integer escaped through (int)  
`?f`  float escaped through (float) and str_replace comma with dot  
`?a` (simple array) placeholder for IN operator (convert array to list of string like 'value', 'value'. Strings escaped through PDO->quote)  
`?A` (associative array) placeholder for SET operator (convert associative array it to string like `field`='value',`field`='value'. Values escaped through PDO->quote)  
`?t` table name placeholder return `table`  
`?p` prepared string
## Examples:
`$database->prepare('SELECT * FROM users WHERE group = ?s AND points > ?i', 'user', 7000);`  
Returns `SELECT * FROM users WHERE group = 'user' AND points > 7000`

`$database->prepare('SELECT * FROM user WHERE name IN(?a)', array('foo', 'bar', 'hello', 'world'));`   
Returns `SELECT * FROM user WHERE name IN('foo', 'bar', 'hello', 'world')`  

`$database->prepare('INSERT INTO users SET ?A', array('name'=>'User Name', 'group'=>'wholesale', 'points'=>7000));`  
Returns `INSERT INTO users SET name = 'User Name', group = 'wholesale', points = '7000'`
