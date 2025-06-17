<?php
$GLOBALS['pdo'] = connectDatabase($dsn, $pdoOptions);

/**
 * Function tries to connect to database using PDO.
 *
 * @param string $dsn
 * @param array $pdoOptions
 * @return PDO
 */
function connectDatabase(string $dsn, array $pdoOptions): PDO
{

    try {
        $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $pdoOptions);
    } catch (\PDOException $e) {
        var_dump($e->getCode());
        throw new \PDOException($e->getMessage());
    }

    return $pdo;
}


/**
 * Returns data from the categories table in the form of an array
 *
 * @return array
 */
function getCategories(): array
{
    $sql = "SELECT name, date_time FROM categories ORDER BY date_time DESC";
    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->execute();

    $number = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            $number,
            $row['name'],
            $row['date_time']
        ];
        $number++;
    }

    return $data;
}
function getUsers(): array{

    $sql = "SELECT firstName,lastName,phoneNumber,userMail FROM  user ";

    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->execute();

    $number = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            $number,
            $row['firstName']." ".$row['lastName'],
            $row['phoneNumber'],
            $row['userMail'],
        ];
        $number++;
    }
return $data;

}
function getVeterinarians(): array{

    $sql = "SELECT firstName,lastName,phoneNumber,veterinarianMail FROM  veterinarian ";

    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->execute();

    $number = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            $number,
            $row['firstName']." ".$row['lastName'],
            $row['phoneNumber'],
            $row['veterinarianMail'],
        ];
        $number++;
    }
    return $data;

}
function getProducts(): array{

    $sql = "SELECT productName,productCost,productRelease FROM  product ";

    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->execute();

    $number = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            $number,
            $row['productName'],
            $row['productCost'],
            $row['productRelease'],

        ];
        $number++;
    }
    return $data;

}
function getRatings(): array{

    $sql = "SELECT r.review,r.reviewTime,v.veterinarianMail,v.firstName,v.lastName FROM 
 review r INNER JOIN veterinarian v ON r.veterinarianId = v.veterinarianId ";

    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->execute();

    $number = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            $number,
            $row['firstName']." ".$row['lastName'],
            $row['review'],
            $row['veterinarianMail'],
            $row['reviewTime'],


        ];
        $number++;
    }
    return $data;

}