<?php
namespace App;
use Aura\SqlQuery\QueryFactory;
use PDO;


class Connection {
    public function make() {
        return new PDO('mysql:host=localhost;dbname=project3', "root", "root");
    }
}

// Connection::make();

class QueryBuilder {

    private  $pdo;
    private  $results;
    private  $queryFactory;


    public function __construct() { // тут не передается PDO $pdo
        
        $this->pdo = new PDO('mysql:host=localhost;dbname=project3', "root", "root");
        //$this->pdo = $pdo;
        $this->queryFactory = new QueryFactory('mysql');
    }
    
// При создании объекта QueryBuilder нужно передавать экземпляр объекта PDO (подключение то есть)

    public function getAll($table) {

        $select = $this->queryFactory->newSelect();

        $select->cols(['*'])
         ->from($table); 

        $sth = $this->pdo->prepare($select->getStatement());

        $sth->execute($select->getBindValues());

        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        return $result;
        
    }


    public function insert($data, $table) {

        $insert = $this->queryFactory->newInsert();

        $insert
            ->into($table)    
            ->cols($data);

            
            //var_dump($insert->getStatement());die; // string(196) "INSERT INTO `products` ( `name`, `description`, `full_text`, `category`, `price` ) VALUES ( :name, :description, :full_text, :category, :price )" 
            $sth = $this->pdo->prepare($insert->getStatement());

            
            $sth->execute($insert->getBindValues());       
                   
            //var_dump($insert->getBindValues()); //array(2) { ["name"]=> NULL ["description"]=> NULL } 

            //var_dump( $_POST['product_name']);die;
            
    }


    public function update($data, $id, $table) { 

        $update = $this->queryFactory->newUpdate();

        $update
            ->table($table)                
            ->cols($data) 
            ->where('id = :id')
            ->bindValue('id', $id);     
            
            //print_r($update->getStatement());die; // "UPDATE `products` SET `id` = :id, `name` = :name, `description` = :description, `full_text` = :full_text, `category` = :category, `price` = :price WHERE id = :id"  

        $sth = $this->pdo->prepare($update->getStatement());
       // print_r($update->getBindValues());die; //Array ( [name] => [description] => [full_text] => [category] => [price] => [id] => 3 ) 
        $sth->execute($update->getBindValues());
        


    }


    public function delete($id, $table) 
    {

        $delete = $this->queryFactory->newDelete();

        $delete
            ->from($table)                
            ->where('id = :id')
            ->bindValue('id', $id);      
        
        // var_dump($delete->getStatement());die; // string(43) "DELETE FROM `products` WHERE id = :id" 

        $sth = $this->pdo->prepare($delete->getStatement());

        //var_dump($delete->getBindValues());die;
        $sth->execute($delete->getBindValues());

    }


    public function findOne($table, $id)
    {
        $select = $this->queryFactory->newSelect();

        $select->cols(['*'])
         ->from($table)
         ->where('id = :id'); 

       $sth = $this->pdo->prepare($select->getStatement());
        //var_dump($select->getStatement());die; // string(56) "SELECT * FROM `products` WHERE id = :id" 

        $sth->execute([
            'id' => $_GET['id'],
        ]);

        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

       return $result;   
       
    }


    public function getCategory($table, $category) {
        $select = $this->queryFactory->newSelect();

        $select->cols(['*'])
         ->from($table)
         ->where('category = :category')
        
         ->bindValue('category', $category);  
         $sth = $this->pdo->prepare($select->getStatement());
         // var_dump($select->getStatement());die; // string(68) "SELECT * FROM `products` WHERE category = :category" 
         
         $category = $_GET['category']; 

         $sth->execute([
            'category' => $category,
            
        ]);

         $result = $sth->fetchAll(PDO::FETCH_ASSOC);

       return $result;   
    }


    public function first()
    {
        return $this->results()[0];
    }

}

