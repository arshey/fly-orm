# FLY ORM
Fly orm , est un mini orm qui vous facilite la vie pour la gestion de vos données.


#### **Qu'est ce qu'un ORM**
L'ORM (Object-Relational Mapping) est une technique de programmation faisant le lien entre le monde de la base de données et le monde de la programmation objet. 
Elle permet de transformer une table en un objet facilement manipulable via ses attributs.

#### **CONFIGURATION FLY ORM** 
la connexion à la base de données via fly orm est très simple :

    
	    Php 
	    use Src\orm\Fly;

        $fly = Fly::setup([


        'TYPE'          => 'mysql',
        'HOSTNAME'      => 'localhost',
        'DBNAME'        => 'flydb',
        'USERNAME'      => 'root',
        'PASSWORD'      => 'root'

        ]);

#### **RECUPERATION DE DONNEES** 
	
	    Php
	    /*
            Chargement de la table
		*/
		$cars = $fly->table('cars');

        /*
            Recuperation de plusieurs enregistrements
		*/
        $all = $cars->get();

        $cars = $cars->get(8);
        /*
            Recuperation d'un enregistrement
		*/
        $first = $cars->first();

        

#### **INSERTION DE DONNEES** 
        Php
	    /*
            Chargement de la table
		*/
		$cars = $fly->table('cars');

        
        $cars->marques = "BMW";
        $cars->serie   = "X5";
        $cars->moteur  = "Essence";
        
        if($cars->create()){
            echo "succès";
        }else{
            echo "erreur";
        }

#### **MISE A JOUR DE DONNEES** 

    Php
    /*
        Recuperation de l'id à modifier
    */
    $cars = $fly->refresh('cars',4);

    $cars->moteur = "Diesel";
    $cars->update();

#### **SUPPRESSION DE DONNEES**
    Php
    /*
        Suppression d'un enregistrement via son id
    */
    $cars = $fly->trash('cars',5);

    if($cars){
        echo "suppression réuissie !";
    }
	
#### **CONDITIONS**

##### **WHERE**

    Php
    /*
        Chargement de la table
    */
    $cars = $fly->table('cars');

    $car  = $cars->where(['id >' => 10 ])->get();

##### **ORDER BY**
    Php
    /*
        Chargement de la table
    */
    $cars = $fly->table('cars');

    $car  = $cars->select('marques')->orderBy('id DESC')->get();

##### **LIMIT**

    Php
    /*
        Chargement de la table
    */

    $cars = $fly->table('cars');

    $car  = $cars->select('marques')->orderBy('id DESC')->get(2,5);

    /*
        OR
    */

    $cars = $fly->table('cars');

    $car  = $cars->limit(2,5)->get();

#### **JOINTURES**

##### **INNER JOIN**

    Php
    /*
        Chargement de la table
    */

    $cars = $fly->table('cars');

    $car  = $cars->select()->innerjoin('proprios' , 'cars.proprio_id = proprios.id')->get();

##### **LEFT JOIN**

    Php
    /*
        Chargement de la table
    */

    $cars = $fly->table('cars');

    $car  = $cars->select()->leftjoin('proprios' , 'cars.proprio_id = proprios.id')->get();


##### **RIGHT JOIN**

    Php
    /*
        Chargement de la table
    */

    $cars = $fly->table('cars');

    $car  = $cars->select()->rightjoin('proprios' , 'cars.proprio_id = proprios.id')->get();
