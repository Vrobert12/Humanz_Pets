<?php
include 'functions.php';
$product_id = (int)$_GET['id'];
$functions = new Functions();
$lang = $functions->language();
$functions->checkAutoLogin();
$pdo = $functions->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);
$product = $pdo->prepare("SELECT * FROM product WHERE productId = ?");
$product->execute([$product_id]);
$product = $product->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die('Product not found.');
}

$userId = $_SESSION['userId'] ?? null;
if (!$userId) {
    die('User not logged in.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['productName']); ?> Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/details.css" rel="stylesheet">
  
</head>
<body>

<div class="container">
    <a class="btn btn-secondary my-3" href="products.php"><?php echo BACK ?></a>

    <div class="product-card row g-4">
        <div class="col-md-5 text-center">
            <img src="pictures/products/<?php echo htmlspecialchars($product['productPicture']); ?>"
                 alt="<?php echo htmlspecialchars($product['productName']); ?>" class="product-img img-fluid">
        </div>
        <div class="col-md-7">
            <h2><?php echo htmlspecialchars($product['productName']); ?></h2>
            <p class="mb-1"><strong><?php echo PRICE ?>:</strong> €<?php echo number_format($product['productCost'], 2); ?></p>
            <p class="mb-1"><strong><?php echo REGTIME ?>:</strong> <?php echo htmlspecialchars($product['productRelease']); ?></p>
            <p class="mt-3"><?php echo nl2br(htmlspecialchars($product['description'] ?? 'No description available.')); ?></p>

            <div class="mt-4">
                <label for="quantity" class="form-label"><?php echo SUM_PROD ?>:</label>
                <input type="number" id="quantity" class="form-control d-inline-block" style="width: 80px;" value="1" min="1" max="99" onchange="updatePrice()">
            </div>

            <div class="mt-3 total-price">
                <?php echo TOTAL_PRICE ?>: €<span id="total-price"><?php echo number_format($product['productCost'], 2); ?></span>
            </div>

            <form action="functions.php" method="POST" class="mt-4">
                <input type="hidden" name="action" value="buyProduct">
                <input type="hidden" name="productId" value="<?php echo $product['productId']; ?>">
                <input type="hidden" name="productName" value="<?php echo $product['productName']; ?>">
                <input type="hidden" name="productPicture" value="<?php echo $product['productPicture']; ?>">
                <input type="hidden" name="productPrice" value="<?php echo number_format($product['productCost'], 2); ?>">
                <input type="hidden" name="userId" value="<?php echo htmlspecialchars($userId); ?>">
                <input type="hidden" name="quantity" id="hidden-quantity" value="1">

                <button type="submit" class="btn btn-primary btn-lg mt-2"><?php echo ADDCART ?></button>
            </form>
        </div>
    </div>
</div>

<script>
    function updatePrice() {
        const quantity = document.getElementById('quantity').value;
        const pricePerUnit = <?php echo $product['productCost']; ?>;
        const totalPrice = quantity * pricePerUnit;

        document.getElementById('total-price').textContent = totalPrice.toFixed(2);
        document.getElementById('hidden-quantity').value = quantity;
    }
</script>

</body>
</html>
