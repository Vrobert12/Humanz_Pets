<?php
include 'functions.php';
$product_id = (int)$_GET['id'];
$functions=new Functions();
$lang=$functions->language();
$functions->checkAutoLogin();
$pdo = $functions->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);
$product = $pdo->prepare("SELECT * FROM product WHERE productId = ?");
$product->execute([$product_id]);
$product = $product->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die('Product not found.');
}

// Assuming userId is stored in the session after login
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

        .quantity-selector {
            width: 60px;
            text-align: center;
        }

        .total-price {
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body style="background: #659df7">
<a class="btn btn-secondary" href="products.php"><?php echo BACK ?></a><br><br>
<div class="details">
    <img src="pictures/<?php echo htmlspecialchars($product['productPicture']); ?>"
         alt="<?php echo htmlspecialchars($product['productName']); ?>">
    <div class="details-content">
        <h1><?php echo htmlspecialchars($product['productName']); ?></h1>
        <p><?php echo PRICE ?>: €<?php echo number_format($product['productCost'], 2); ?></p>
        <p><?php echo REGTIME ?>: <?php echo htmlspecialchars($product['productRelease']); ?></p>
        <p><?php echo nl2br(htmlspecialchars($product['description'] ?? 'No description available.')); ?></p>

        <!-- Quantity selection and price update -->
        <div>
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" class="quantity-selector" value="1" min="1" max="99" onchange="updatePrice()">
        </div>
        <div class="total-price">
            <p>Total Price: €<span id="total-price"><?php echo number_format($product['productCost'], 2); ?></span></p>
        </div>

        <!-- Form to submit purchase -->
        <form action="functions.php" method="POST">
            <input type="hidden" name="action" value="buyProduct">
            <input type="hidden" name="productId" value="<?php echo $product['productId']; ?>">
            <input type="hidden" name="productName" value="<?php echo $product['productName']; ?>">
            <input type="hidden" name="productPicture" value="<?php echo $product['productPicture']; ?>">
            <input type="hidden" name="productPrice" value="<?php echo number_format($product['productCost'],2);?>"
            <input type="hidden" name="userId" value="<?php echo htmlspecialchars($userId); ?>">
            <input type="hidden" name="quantity" id="hidden-quantity" value="1">
            <button type="submit" class="btn btn-primary">
                <?php echo ADDCART ?>
            </button>
        </form>
    </div>
</div>

<script>
    function updatePrice() {
        const quantity = document.getElementById('quantity').value;
        const pricePerUnit = <?php echo $product['productCost']; ?>;
        const totalPrice = quantity * pricePerUnit;

        // Update displayed total price
        document.getElementById('total-price').textContent = totalPrice.toFixed(2);

        // Update the hidden quantity input field in the form
        document.getElementById('hidden-quantity').value = quantity;
    }
</script>
</body>
</html>
