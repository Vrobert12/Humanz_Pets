<?php
include 'functions.php';

$functions=new Functions();
$lang=$functions->language();
$functions->checkAutoLogin();
$pdo = $functions->connect($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $pdoOptions);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Product Page</title>
    <script>
        const lang = '<?php echo $lang; ?>';
    </script>
    <script src="sureCheck.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .header-container {
            position: relative;
            text-align: center;
            margin: 20px 0;
        }

        .header-container h1 {
            margin: 0;
            display: inline-block;
        }

        .back-button {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        .product {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin: 10px;
            width: 320px; /* Set a fixed width */
            text-align: center;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            flex-shrink: 0; /* Prevent shrinking */
        }

        .product:hover {
            transform: scale(1.05);
        }

        .product img {
            width: 100%;
            height: 40%;
            border-radius: 5px;
        }

        .cart {
            margin: 20px auto;
            max-width: 800px;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .cart-item {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }

        .cart-item img {
            width: 80px;
            height: 80px;
            margin-right: 15px;
            border-radius: 5px;
        }

        .cart-item-details {
            flex-grow: 1;
        }

        .cart-item h3 {
            margin: 0;
            font-size: 18px;
        }

        .cart-item p {
            margin: 5px 0;
            color: #666;
        }

        .cart-item-price {
            font-size: 16px;
            font-weight: bold;
            color: #28a745;
        }

        .new-product {
            margin-top: 20px;
            padding: 15px;
            background-color: #e9f7ef;
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align: center;
            display: inline-block;
            width: 220px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .new-product:hover {
            transform: scale(1.05);
            background-color: #a8d5ba;
        }

        .new-product button {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .new-product button:hover {
            background-color: #218838;
        }

        /* Flexbox Grid */
        .d-flex {
            display: flex;
            flex-wrap: wrap;
            justify-content: center; /* Centers the items */
            gap: 10px; /* Adds space between items */
        }

    </style>
</head>
<body style="background: #659df7">
<div class="header-container">
    <a class="btn btn-secondary back-button" href="checkProducts.php"><?php echo BACK ?></a>
    <h1><?php echo PRODUCT ?></h1><br>
    <?php
    if (isset($_SESSION['message']) && $_SESSION['message']!=4) {
        echo "<h3 style='color: red'>" . $_SESSION['message'] . "</h3>";
        unset($_SESSION['message']);
    }
    if(isset($_SESSION['message']) && $_SESSION['message']==4)
        unset($_SESSION['message']);
    ?>
</div>

<?php
$totalPrice = 0;
$sql = "SELECT * FROM  user_product_relation up
        INNER JOIN user u ON u.userId = up.userId 
        WHERE u.userId = :userId AND productPayed=0";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':userId', $_GET['user']);
$stmt->execute();
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(empty($cartItems)) {
    header('Location:checkProducts.php');
    exit();
}
foreach ($cartItems as $price) {
    $totalPrice = $totalPrice + (float)$price['sum'] * $price['price'];
}
?>
<div class="cart">
    <h2><?php echo CART ?></h2>
    <h3><?php echo TOTAL_PRICE.": €" . number_format($totalPrice, 2); ?></h3>
    <form action="functions.php" method="post">
        <input type="hidden" name="action" value="PayAllFromCart">
        <input type="hidden" name="userId" value="<?php echo htmlspecialchars($_GET['user']); ?>">
        <input type="submit" class="btn btn-primary" value="<?php echo PAY_ALL_PRODUCTS?>" onclick="confirmAllProductIsPayed(event)">
    </form>
    <ul id="cart-list">
        <?php foreach ($cartItems as $item): ?>
            <li class="cart-item">
                <img  src="pictures/products/<?php echo htmlspecialchars($item['productPicture']); ?>"
                      alt="<?php echo htmlspecialchars($item['productName']); ?>">
                <div class="cart-item-details">
                    <h3><?php echo htmlspecialchars($item['productName']); ?></h3>
                    <p><?php echo SUM_PROD?>: <?php echo "<b>".$item['sum']."</b>"; ?></p>
                    <p class="cart-item-price">€<?php echo number_format($item['price'], 2) * $item['sum']; ?></p>
                </div>
                <form action="functions.php" method="post">
                    <input type="hidden" name="action" value="deleteFromCart">
                    <input type="hidden" name="cartId" value="<?php echo htmlspecialchars($item['userProductRelationId']); ?>">
                    <input type="submit" class="btn btn-danger" value="<?php echo DELETE_PRODUCT?>" onclick="confirmDeletingCart(event)">
                </form>
                <form action="functions.php" method="post">
                    <input type="hidden" name="action" value="PayFromCart">
                    <input type="hidden" name="userId" value="<?php echo htmlspecialchars($item['userId']); ?>">
                    <input type="hidden" name="cartId" value="<?php echo htmlspecialchars($item['userProductRelationId']); ?>">
                    <input type="submit" class="btn btn-primary" value="<?php echo PAY_PRODUCT?>" onclick="confirmProductIsPayed(event)">
                </form>

            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
