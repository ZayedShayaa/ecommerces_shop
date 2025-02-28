<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل المنتج</title>
</head>
<style>
    
    main {
        display: flex;
        flex-wrap: wrap;
    }

    .container {
        width: 90;
        height: auto;
        margin: 20px auto;
        border-radius: 8px;
    }

    .product_img {
        float: left;
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .product_img img {
        width: 400px;
        height: 400px;
        margin-left: 40px;
        margin-bottom: 20px;
    }

    .product_info {
        float: right;
        width: 400px;
        height: 400px;
        text-align: center;
        font-size: 20px;
        margin-right: 50px;
        padding: 10px 10px;
        margin-top: 30px;
    }

    .product_title {
        margin: 10px 0;
    }

    .product_price {
        color: #e67e22;
        margin: 10px 0;
    }

    .product_description {
        font-size: 16px;
        line-height: 1.5;
    }

    .add_cart {
    width: 100%;
    height: 35px;
    background-color: #0073ff;
    color: white;
    border: none;
    font-size: 16px;
    font-weight: bold;
    margin-top: 10px;
    padding: 10px;
    cursor: pointer;
    border-radius: 5px;
    transition: 0.3s;
}

    .add_cart:hover {
        background-color: #e67e22;
    }

    .recently_added {
        float: right;
        width: 30%;
        margin-top: 30px;
        border-radius: 8px;
        padding: 10px 10px;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 1.0);
    }

    .added_img img {
        float: right;
        margin: 10px 10px;
        width: 70px;
        height: 70px;
        margin-right: 5px;
        border-radius: 10px;
    }

    .comment_info {
        float: left;
        width: 50%;
        height: auto;
        margin: 20px 10px;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 1.0);
    }

    h5 {
        font-size: 20px;
        margin-top: 20px;
        text-align: center;
        color: black;
    }

    textarea {
        text-align: center;
        width: 80%;
        margin-top: 20px;
        margin-left: 50px;
        margin-bottom: 10px;
        padding: 10px;
        border: 1px solid #ccc;
        height: 50px;
    }

    .add_comment {
        width: 100px;
        height: 35px;
        margin-left: 40px;
        padding: 10px 10px;
        background-color: #fff;
        border-radius: 5px;
    }

    .add_comment:hover {
        background-color: #e67e22;
    }

    .comments {
        margin-top: 10px;
    }

    .comment {
        color: black;
        font-size: larger;
        margin: 5px 5px;
        text-align: center;
        padding: 10px;
        background-color: #fff;
        border: 1px solid #ddd;
        margin-bottom: 10px;
        border-radius: 5px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
  
</style>

<body>
    <main>
        <?php


        include("file/header.php");
       

        $id = $_GET['id'];
        if (isset($_GET['id'])) {
            $query = "SELECT *FROM product WHERE id='$id'";
            $result = $conn->prepare($query);
            $result->execute();
            $row = $result->fetch(PDO::FETCH_ASSOC);
        }


        ?>
        <!-- start image -->
        <div class="container">
            <div class="product_img">
                <img src="uploade/img//<?php echo $row['proimg']; ?>">
            </div>
            <!-- end image -->

            <!-- start information -->
            <div class="product_info">
                <h1 class="product_title"><?php echo $row['proname']; ?></h1>
                <h2 class="product_price"><?php echo $row['proprice']; ?>&nbsp;السعر</h2>
                <h3><?php echo $row['prosize']; ?> &nbsp;:المقاسات </h3>
                <h4 class="product_description">تفاصيل المنتج</h4>
                <p><?php echo $row['prodescription']; ?></p>
                <hr>
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
        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
            <button class="add_cart" type="submit" name="add_to_cart">
                <i class="fa-solid fa-cart-plus">&nbsp; &nbsp;</i>أضف الى السلة
            </button>
        </div>
      </form>
            </div>
            <!-- end information -->
        </div>
    </main>
    <hr>
    <!-- start recently added -->
    <div class="container">
        <div class="recently_added">
            <h4>منتجات حديثة</h4>
            <?php
            $query = "SELECT *FROM product WHERE id!='$id' ORDER BY rand() LIMIT 3";
            $result = $conn->prepare($query);
            $result->execute();
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            ?>
                <div class="added_img"><a href="detalis.php?id=<?php echo $row['id']; ?>">
                        <img src="uploade/img//<?php echo $row['proimg']; ?>">

                    </a></div>
            <?php
            }
            ?>
        </div>
        <!-- end recently added -->
        <!-- start comment  -->
        <div class="comment_info">
            <h5>هل تود تقييم هذا المنتج</h5>
            <?php
            @$add_comment = $_POST['add_comment'];
            @$text_comment = $_POST['text_comment'];
            @$id_pro = $_GET['id'];
            if (isset($_GET['id'])) {
                if (isset($add_comment)) {

                    $query = "INSERT INTO  review(text_comment,id_pro) VALUES('$text_comment','$id_pro')";
                    $result = $conn->prepare($query);
                    $result->execute();
                }
            }
            ?>
            <form action="" method="post">
                <textarea name="text_comment" placeholder="قيم هذا المنتج من فضلك" required></textarea>
                <button class="add_comment" type="submit" name="add_comment">ارسال </button>
            </form>
            <h5>تقيمات العملاء</h5>
            <?php
            $id = $_GET['id'];
            if (isset($_GET['id'])) {
                $query = "SELECT *FROM review WHERE id_pro='$id'";
                $result = $conn->query($query);
                $result->execute();
            }
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            ?>
                <div class="comments">
                    <div class="comment"> <a href="detalis.php?id=<?php echo $row['id']; ?>"></a><?php echo $row['text_comment']; ?> </div>
                </div>
            <?php } ?>  <br>
    <br>
<br>
<br>
<br>
        </div>
        <!-- end comment  -->

    </div>
  


<?= include("file/footer.php");?>
<script src="inc&dec.js"></script>
</body>

</html>
