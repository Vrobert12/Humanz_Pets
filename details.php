<?php
include 'functions.php';
if (!isset($_GET['id'])) {
    die('Product ID is required.');
}

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
include_once "lang_$lang.php";

$product_id = (int)$_GET['id'];
$functions = new Functions();
$functions->checkAutoLogin();
$pdo = $functions->connect($GLOBALS['dsn'], PARAMS['USER'], PARAMS['PASSWORD'], $GLOBALS['pdoOptions']);
$product = $pdo->prepare("SELECT * FROM product WHERE productId = ?");
$product->execute([$product_id]);
$product = $product->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die('Product not found.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title><?php echo htmlspecialchars($product['productName']); ?> Details</title>
    <style>
        .details {
            display: flex;
            flex-direction: row;
            margin-left: 30px;
        }

        .details-content {
            margin-left: 40px;
        }
    </style>
</head>
<body>
<a class="btn btn-secondary" href="products.php"><?php echo BACK ?></a><br><br>
<div class="details">
    <img src="pictures/<?php echo htmlspecialchars($product['productPicture']); ?>"
         alt="<?php echo htmlspecialchars($product['productName']); ?>">
    <div class="details-content">
        <h1><?php echo htmlspecialchars($product['productName']); ?></h1>
        <p><?php echo PRICE?>: €<?php echo number_format($product['productCost'], 2); ?></p>
        <p><?php echo REGTIME?>: <?php echo htmlspecialchars($product['productRelease']); ?></p>
        <p><?php echo nl2br(htmlspecialchars($product['description'] ?? 'No description available.')); ?></p>
        <button class="btn btn-primary"
                onclick="addToCart(<?php echo $product['productId']; ?>, '<?php echo htmlspecialchars($product['productName']); ?>')">
            <?php echo ADDCART?>
        </button>
    </div>
</div>
</body>
<script>
    function addToCart(productId, productName) {
        alert(productName + ' added to cart!');
    }
</script>
</body>
</html>
