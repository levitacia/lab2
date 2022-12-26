<?php
	require_once 'connect.php';

    // Реакция на пост
	if (isset($_POST['liked'])) {
		$post_id = $_POST['postid'];
		$result = mysqli_query($connect, "SELECT * FROM post WHERE id = $post_id");
		$row = mysqli_fetch_array($result);
		$like = $row['likes'];
        if ($like < 0){
            $like = 0;
        }

		mysqli_query($connect, "INSERT INTO likes (postid) VALUES ($post_id)");
		mysqli_query($connect, "UPDATE post SET likes = $like + 1 WHERE id = $post_id");

		echo $like + 1;
		exit();
	}

    if (isset($_POST['unliked'])) {
		$post_id = $_POST['postid'];
		$result = mysqli_query($connect, "SELECT * FROM post WHERE id = $post_id");
		$row = mysqli_fetch_array($result);
		$like = $row['likes'];
        if ($like < 0){
            $like = 0;
        }

		mysqli_query($connect, "DELETE FROM likes WHERE postid = $post_id");
		mysqli_query($connect, "UPDATE post SET likes = $like - 1 WHERE id = $post_id");

		echo $like - 1;
		exit();
	}

    //Пагинация
	$post = mysqli_query($connect, "SELECT * FROM post ORDER BY id DESC");
    $posts = mysqli_fetch_all($post);

    $total = count($posts); // кол-во постов
    $per_page = 5; // кол-во постов на одну страницу
    $count_page = ceil( $total / $per_page ); // кол-во страниц
    $page = $_GET['page']??1; // определение страницы по GET запросу
    $page = (int)$page;

    if(!$page || $page < 1){
        $page = 1;
    } else if ($page > $count_page) {
        $page = $count_page;
    }
    $start = ($page - 1) * $per_page; // начало распечатки элементов постранично

?>
<!DOCTYPE html>
<html>
    <head>
	<title>Forum</title>
    <meta charset="utf-8">
</head>
<body>
    <h1>Forum</h1>

    <div class ="gl">

        <div class="form">
            <form action="post.php" method="POST" enctype="multipart/form-data">
                <label>Login:</label><br>
                <input type="text" class="login" name ="login"><br>
                <textarea name="post"></textarea>
				<input type="file" Value="Изображение" name="img" style="margin: 10px;">
                <br>
                <input type="Submit" Value="Опубликовать" class="button">
                <input type="reset" Value="Очистить" class="button">
            </form>
        </div>

        <h2>Feed</h2>

		<?php
		$posts = array_slice($posts, $start, $per_page); // выборка по n строк из бд
		foreach ($posts as $post) {
			?>
			<div class="post">
            	<div class="user">
                	<?php echo $post[1]; ?>
            	</div>

            	<div class="text">
                	<?php echo $post[2]; ?><br>
					<?php
					if (!empty($post[5])){
						?>
						<img src="<?php echo $post[5] ?>" width="50%" alt=""
						style="border-radius: 8px; margin-left: auto; margin-right: auto; display: block;"/>
					<?php } ?>
					<div class="update"
					style="font-weight: bold; margin-top: 15px;">
						<a href="update.php?id=<?=$post[0]?>" style="text-decoration: none;">Изменить</a>
					</div>
					<div class="delete"
					style="font-weight: bold; margin-top: 5px;">
						<a href="delete.php?id=<?=$post[0]?>" style="text-decoration: none;  color:rgba(194, 0, 0, 0.74);">Удалить</a>
					</div>
            	</div>
            	<div style="margin-left: 45px; font-weight: bold;">
                	<?php echo $post[3]; ?>
            	</div>
            	<div style="font-size: 20px; margin: 10px 15px;">
					<?php
					$results = mysqli_query($connect, "SELECT * FROM likes WHERE postid=".$post[0]."");
					if (mysqli_num_rows($results) == 1 ): ?>
						<span style="color: #092A5B;" class="unlike fa fa-thumbs-down" data-id="<?php echo $post[0]; ?>"></span>
						<span style="color: rgba(194, 0, 0, 0.74);;" class="like hide fa fa-thumbs-o-up" data-id="<?php echo $post[0]; ?>"></span>
					<?php else: ?>
                    	<span style="color: #092A5B;;" class="unlike hide fa fa-thumbs-down" data-id="<?php echo $post[0]; ?>"></span>
						<span style="color: rgba(194, 0, 0, 0.74);;"  class="like fa fa-thumbs-o-up" data-id="<?php echo $post[0]; ?>"></span>
					<?php endif ?>
					<span class="likes_count"><?php echo $post[4]; ?> likes</span>
            	</div>
			</div>
		<?php
		}
        ?>
        <CENTER>
            <?php
            for ($i = 1; $i <= $count_page; $i++){
                ?>
                <a style="font-weight: bold"
                href='?page=<?=$i?>'><?=$i?></a>
                <?php
            }
            ?>
        </CENTER>
    </div>
    <script src="/app/js/jquery.min.js"></script>
    <script>
       $(document).ready(function(){

       	$('.like').on('click', function(){
       		var post_id = $(this).data('id');
       		$post = $(this);

       		$.ajax({
       			url: 'index.php',
       			type: 'post',
       			data: {
       				'liked': 1,
       				'postid': post_id
       			},
       			success: function(response){
       				$post.parent().find('span.likes_count').text(response + " likes");
       				$post.addClass('hide');
       				$post.siblings().removeClass('hide');
       			}
       		});
       	});

       	$('.unlike').on('click', function(){
       		var post_id = $(this).data('id');
       	    $post = $(this);

       		$.ajax({
       			url: 'index.php',
       			type: 'post',
       			data: {
       				'unliked': 1,
       				'postid': post_id
       			},
       			success: function(response){
       				$post.parent().find('span.likes_count').text(response + " likes");
       				$post.addClass('hide');
       				$post.siblings().removeClass('hide');
       			}
       		});
       	});
       });

    </script>
</body>
</html>
