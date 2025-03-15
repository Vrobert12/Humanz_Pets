<?php
include 'functions.php';

$functions = new Functions();
$lang = $functions->language();
$functions->checkAutoLogin();
$pdo = $functions->connect($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $pdoOptions);
if(isset($_SESSION['get_lang']))
    $productLanguage=$_SESSION['get_lang'];

else
    $productLanguage=$_SESSION['userLang'];
if (isset($_POST['searchAction']) && !empty($_POST['searchProduct'])) {
    $searchTerm = "%" . $_POST['searchProduct'] . "%";
    $stmt = $pdo->prepare("SELECT productId, productName, productPicture, description, productCost FROM product WHERE productLanguage=:userLanguage AND productName LIKE :searchTerm");
    $stmt->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
    $stmt->bindValue(':userLanguage', $productLanguage, PDO::PARAM_STR);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo '<div id="productsList" class="d-flex flex-wrap justify-content-center">';
    foreach ($products as $product) {
        echo '<div class="product">
            <img src="pictures/products/' . htmlspecialchars($product['productPicture']) . '"
                 alt="' . htmlspecialchars($product['productName']) . '">
            <h2>' . htmlspecialchars($product['productName']) . '</h2>
            <p>' . PRICE . ': €' . number_format($product['productCost'], 2) . '</p>
            <a href="details.php?id=' . $product['productId'] . '" class="btn btn-primary">' . DETAILS . '</a>';

        if (isset($_SESSION['privilage']) && $_SESSION['privilage'] === 'Admin') {
            echo '<form action="updateProduct.php?id=' . $product['productId'] . '" method="post" enctype="multipart/form-data">
                    <button type="submit" class="btn btn-warning">' . MODIFY . '</button>
                  </form>';
            echo '<form action="functions.php" method="post">
                    <input type="hidden" name="action" value="deleteFromProduct">
                    <input type="hidden" name="productId" value="' . $product['productId'] . '">
                    <input type="submit" class="btn btn-danger" value="Delete Product">
                  </form>';
        }

        echo '</div>';
    }
    echo '</div>'; // productsList lezárása
    exit; // AJAX esetén itt leáll a PHP feldolgozás
} else {
    $stmt = $pdo->prepare("SELECT productId, productName, productPicture, description, productCost FROM product WHERE productLanguage = :userLanguage");
    $stmt->bindValue(':userLanguage', $productLanguage, PDO::PARAM_STR);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

}

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
        function performSearch() {
            const searchTerm = document.getElementById('searchProduct').value;

            if (searchTerm.trim() === "") {
                location.reload(); // Reloads the page when the search is cleared
                return;
            }

            const formData = new FormData();
            formData.append('searchProduct', searchTerm);
            formData.append('searchAction', '1');

            fetch('products.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('productsList').innerHTML = data; // Replaces instead of appending
                })
                .catch(error => console.error('Error:', error));
        }

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
            width: 300px; /* Adjusted width */
            text-align: center;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            flex-shrink: 0;
        }

        .product:hover {
            transform: scale(1.05);
        }

        .product img {
            width: 100%;
            height: auto;
            max-height: 200px; /* Adjusted max height */
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

        #productsList {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start; /* Align items to the left */
        }

    </style>
</head>

<body style="background: #659df7">

<div class="header-container">
    <a class="btn btn-secondary back-button" href="index.php"><?php echo BACK ?></a>
    <h1><?php echo PRODUCT ?></h1><br>
    <?php
    if (isset($_SESSION['message']) && $_SESSION['message'] != 4) {
        echo "<h3 style='color: red'>" . $_SESSION['message'] . "</h3>";
        unset($_SESSION['message']);
    }
    if (isset($_SESSION['message']) && $_SESSION['message'] == 4)
        unset($_SESSION['message']);
    ?>
</div>

<!-- Search Form -->
<div class="d-flex flex-wrap justify-content-center">
    <div class="new-product">
        <form id="searchForm" method="post">
            <input type="text" id="searchProduct" name="searchProduct" placeholder="Product Name" oninput="performSearch()">
            <input type="hidden" name="searchAction" value="1"> <!-- Add a search action field to differentiate the request -->
        </form>
    </div>
</div>

<!-- Add New Product Section (Admin only) -->
<?php if (isset($_SESSION['privilage']) && $_SESSION['privilage'] === 'Admin'): ?>
    <div class="d-flex flex-wrap justify-content-center">
        <div class="new-product">
            <form action="addProduct.php" method="get">
                <button type="submit"><?php echo ADDPRODUCT ?></button>
            </form>
        </div>
    </div>
<?php endif; ?>

<!-- Products List -->
<div id="productsList" class="d-flex flex-wrap justify-content-center">
    <?php foreach ($products as $product): ?>
        <div class="product">
            <img src="pictures/products/<?php echo htmlspecialchars($product['productPicture']); ?>"
                 alt="<?php echo htmlspecialchars($product['productName']); ?>">
            <h2><?php echo htmlspecialchars($product['productName']); ?></h2>
            <p><?php echo PRICE ?>: €<?php echo number_format($product['productCost'], 2); ?></p>
            <a href="details.php?id=<?php echo $product['productId']; ?>" class="btn btn-primary"><?php echo DETAILS ?></a>
            <?php if (isset($_SESSION['privilage']) && $_SESSION['privilage'] === 'Admin')
                echo ' <form action="updateProduct.php?id='.$product['productId'].'" method="post" enctype="multipart/form-data">
 <input type="hidden" name="productId" value="' . $product['productId'] . '">
 <input type="hidden" name="productName" value="'.$product['productName'].'" class="btn btn-primary">
  <input type="hidden" name="productCost" value="'.$product['productCost'].'" class="btn btn-primary">
    <input type="hidden" name="productDescription" value="'.$product['description'].'" class="btn btn-primary">
   <input type="hidden" name="productPicture" value="'.$product['productPicture'].'" class="btn btn-primary">
               <br> <button type="submit" class="btn btn-warning">'.MODIFY.'</button>
            </form>';
            echo '   <br><form action="functions.php" method="post">
                    <input type="hidden" name="action" value="deleteFromProduct">
                    <input type="hidden" name="productId" value="'.$product['productId'].'">
                    <input type="submit" class="btn btn-danger" value="Delete Product" onclick="confirmDeletingProduct(event)">
                </form>';
            ?>
        </div>
    <?php endforeach; ?>
</div>

<?php
$totalPrice = 0;
$sql = "SELECT * FROM user_product_relation up
        INNER JOIN user u ON u.userId = up.userId 
        WHERE u.userId = :userId AND up.productPayed = 0";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':userId', $_SESSION['userId']);
$stmt->execute();
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($cartItems as $price) {
    $totalPrice = $totalPrice + (float)$price['sum'] * $price['price'];
}
?>
<div class="cart">
    <h2><?php echo CART ?></h2>
    <h3><?php echo TOTAL_PRICE . ": €" . number_format($totalPrice, 2); ?></h3>
    <ul id="cart-list">
        <?php foreach ($cartItems as $item): ?>
            <li class="cart-item">
                <img src="pictures/products/<?php echo htmlspecialchars($item['productPicture']); ?>"
                     alt="<?php echo htmlspecialchars($item['productName']); ?>">
                <div class="cart-item-details">
                    <h3><?php echo htmlspecialchars($item['productName']); ?></h3>
                    <p><?php echo SUM_PROD ?>: <?php echo "<b>" . $item['sum'] . "</b>"; ?></p>
                    <p class="cart-item-price">€<?php echo number_format($item['price'], 2) * $item['sum']; ?></p>
                </div>
                <form action="functions.php" method="post">
                    <input type="hidden" name="action" value="deleteFromCart">
                    <input type="hidden" name="cartId" value="<?php echo htmlspecialchars($item['userProductRelationId']); ?>">
                    <input type="submit" class="btn btn-danger" value="Delete Product">
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

</body>

</html>
