<?= include("file/header.php"); ?>

<link rel="stylesheet" href="stylepro.css?v=<?php echo time(); ?>">
<style>
    @media (max-width: 600px) {
    .product {
        width: 90px; /* تقليل عرض المنتج أكثر */
        padding: 3px;
        margin: 5px;
    }

    .product_img img {
        width: 75px;
        height: 65px;
        border-radius: 8px;
    }

    .product_name a, .product_price a, .product_description a, .product_section a {
        font-size: 9px; /* تصغير النصوص أكثر */
    }

    .add_cart {
        height: 22px;
        font-size: 10px;
        padding: 5px;
    }

    .qty_input input {
        width: 30px; /* تصغير حجم إدخال الكمية */
        font-size: 9px;
    }

    .qty_count_mins, .qty_count_add {
        padding: 2px 2px;
        font-size: 9px;
       
}}



</style>
<main>
    <?php 
    $query = "SELECT * FROM product";
    $result = $conn->query($query);
    
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    ?>
    <div class="product">
        <!-- img -->
        <div class="product_img">
            <a href="detalis.php?id=<?= $row['id']; ?>">
                <img src="uploade/img/<?= $row['proimg']; ?>">
                <span class="unavailable"><?= $row['prounv']; ?></span>
            </a>
        </div>
        <!-- section -->
        <div class="product_section">
            <a href="detalis.php?id=<?= $row['id']; ?>"><?= $row['prosection']; ?></a>
        </div>
        <!-- name -->
        <div class="product_name">
            <a href="detalis.php?id=<?= $row['id']; ?>"><?= $row['proname']; ?></a>
        </div>
        <!-- price -->
        <div class="product_price">
            <a href="detalis.php?id=<?= $row['id']; ?>"><?= $row['proprice']; ?>&nbsp;السعر</a>
        </div>
        <!-- description -->
        <div class="product_description">
            <a href="detalis.php?id=<?= $row['id']; ?>"><i class="fa-solid fa-eye"></i>تفاصيل المنتج: اضغط هنا</a>
        </div>

        <form action="val.php" method="POST">
            <!-- Quantity -->
            <div class="qty_input">
                <button type="button" class="decrease-btn">-</button>
                <input type="number" name="quantity" class="quantity-input" value="1" min="1" max="7">
                <button type="button" class="increase-btn">+</button>
            </div>
            
            <!-- Hidden Product ID -->
            <input type="hidden" name="product_id" value="<?= $row['id']; ?>">

            <!-- Submit -->
            <div class="submit">
                <button class="add_cart" type="submit" name="add_to_cart">
                    <i class="fa-solid fa-cart-plus">&nbsp; &nbsp;</i>أضف الى السلة
                </button>
            </div>
        </form>
    </div>
    <?php } ?>
</main>
<br><br><br><br>
<?= include("file/footer.php"); ?>

<script src="inc&dec.js"></script>
