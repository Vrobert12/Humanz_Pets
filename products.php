<?php
include 'functions.php';

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
include_once "lang_$lang.php";
$message = $_SESSION['message'] ?? '';
$functions = new Functions();
$functions->checkAutoLogin();
$pdo = $functions->connect($GLOBALS['dsn'], PARAMS['USER'], PARAMS['PASSWORD'], $GLOBALS['pdoOptions']);
$products = $pdo->query("SELECT productId, productName, productPicture, productCost FROM product")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Product Page</title>
    <style>
        .product {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 15px;
            display: inline-block;
            width: 200px;
            text-align: center;
        }
        .product img {
            width: 100%;
            height: auto;
        }
        .cart {
            margin: 20px;
        }
    </style>
</head>
<body>
<h1><?php echo PRODUCT?></h1>
<a class="btn btn-secondary" href="index.php"><?php echo BACK?></a>
<div>
    <?php
    $products = $pdo->query("SELECT productId, productName, productPicture, productCost FROM product")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($products as $product): ?>
        <div class="product">
            <img src="pictures/<?php echo htmlspecialchars($product['productPicture']); ?>" alt="<?php echo htmlspecialchars($product['productName']); ?>">
            <h2><?php echo htmlspecialchars($product['productName']); ?></h2>
            <p><?php echo PRICE?>: $<?php echo number_format($product['productCost'], 2); ?></p>
            <a href="details.php?id=<?php echo $product['productId']; ?>">Details</a>
        </div>
    <?php endforeach; ?>
</div>
<div class="cart">
    <h2><?php echo CART?></h2>
    <ul id="cart-list">
        <!-- Cart items will be dynamically added here -->
    </ul>
</div>
<script>
    const cartList = document.getElementById('cart-list');

    function addToCart(productId, productName) {
        const listItem = document.createElement('li');
        listItem.textContent = productName;
        cartList.appendChild(listItem);
    }
</script>
</body>
</html>
