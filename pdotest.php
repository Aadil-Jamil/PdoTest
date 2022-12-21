<?php
$host = '127.0.0.1';
$db = 'pdo_test_db';
$user = 'root';
$pass = 'salsoft2020@';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
}
$stmt = $pdo->query('SELECT 
                        categories.id as category_id,
                        categories.name as category_name,
                        products.id as product_id, 
                        products.name as product_name                        
                    FROM 
                        categories,category_product,products
                    WHERE 
                        categories.id = category_product.category_id AND
                        products.id = category_product.product_id 
                ');
$result = $stmt->fetchAll(PDO::FETCH_OBJ);

$hashTable = [];
$data = [];
foreach ($result as $category) {
    // search the category id in hash table and get index if found 
    $index = array_search($category->category_id, $hashTable);
    // if index not found we will push category data with products array in it 
    if ($index === false) {
        $hashTable[] = $category->category_id;
        array_push($data, [
            'id' => $category->category_id,
            'name' => $category->category_name,
            'products' => [
                [
                    'id' => $category->product_id,
                    'name' => $category->product_name,
                ]
            ],
        ]);
        continue;
    }
    // if index found we will push only product in data array;
    array_push($data[$index]['products'],[
        'id' => $category->product_id,
        'name' => $category->product_name,
    ]);
}
// clearing unwanted memory
unset($hashTable);
print_r(json_encode($data));

?>
