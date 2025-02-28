<?php
include("file/header.php");
?>
<style>
    .notification {
        width: 1000px;
        height: 50px;
        background-color: wheat;
        border: 2px solid red;
        margin: 40px;
        padding: 10px;
        font-size: 40px;
        color: black;
        text-align: center;
    }
    .product { 
        display: inline-block;
         width: 200px; /* Adjust the width according to your layout */ 
        margin: 10px;
         vertical-align: top;        
        } 
    .product_img img { 
        max-width: 100%;
     }
</style>

<link rel="stylesheet" href="stylepro.css?v=<?php echo time(); ?>">
<?php
if (isset($_GET['section'])) {
    $section = $_GET['section'];
    $query = "SELECT * FROM product WHERE prosection = '$section'";
    $result = $conn->prepare($query);
   
    $result->execute();

    if($result->rowCount()> 0){
        while($row=$result->fetch(PDO::FETCH_ASSOC)){
            echo '
             <div class="product">
            <!-- img -->
            <div class="product_img"><a href="detalis.php?id=' . $row['id'].'">
                <img src="uploade/img//' .$row['proimg'] .'">
                <span class="unvailable"> '. $row['prounv'].'  </span>
                <a href=""></a>
            </div>
            <!-- section -->
            <div class="product_section"><a href="">'.$row['prounv'].'</a>
            </div>
            <!-- name -->
            <div class="product_name">
                <a href="detalis.php?id=' . $row['id'].'">'.$row['proname'].'</a>
            </div>
            <!-- price -->
            <div class="product_price">
                <a href="detalis.php?id=' . $row['id'].'">'.$row['proprice'].'&nbsp;السعر</a>
            </div>
            <!-- description -->
            <div class="product_description">
                <a href="detalis.php?id=' . $row['id'].'"><i class="fa-solid fa-eye">'.$row['prodescription'].'</i>تفاصيل المنتج </a>
            </div>
            <form action="val.php" method="POST">
       <!-- Quantity -->
            <div class="qty_input">
                <button type="button" class="decrease-btn">-</button>
                <input type="number" name="quantity" class="quantity-input" value="1" min="1" max="7">
                <button type="button" class="increase-btn">+</button>
            </div><br>
         <!-- Quantity -->
           <!-- submit -->
        <div class="submit">
        <input type="hidden" name="product_id" value="' . $row['id'].'">
            <button class="add_cart" type="submit" name="add_to_cart">
                <i class="fa-solid fa-cart-plus">&nbsp; &nbsp;</i>أضف الى السلة
            </button>
        </div>
      </form>
             <!-- submit -->
            </div>
            
            ';
           
        }
    }else{
        echo    ' <div class="notification">المنتج الذي تبحث عنه غير موجود حاليا</div>';
    }
    }
    ?>
    
    <br><br><br><br><br>
    <?= include("file/footer.php");?>
    <script src="inc&dec.js"></script>
