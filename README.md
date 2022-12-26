Реализация шаблона CRUD
========================
Задание
------------------------
Разработать и реализовать клиент-серверную информационную систему, реализующую механизм CRUD. Система предназначена для анонимного общения в сети интернет.

Интерфейс системы представляет собой веб-страницу с лентой заметок, отсортированных в обратном хронологическом порядке и форму добавления новой заметки. В ленте отображаются последние 100 заметок.

Возможности пользователей:

- добавление текстовых заметок в общую ленту
- реагирование на чужие заметки(лайки)
- добавление комментариев к чужим заметкам
- "раскрывающиеся" комментарии

Ход работы
------------------------

### [1. Пользовательский интерфейс](https://www.figma.com/file/VtJEYULVUZ)

#### 2. Пользовательский сценарий работы

Пользователь попадает на главную страницу index.php, вводит любое текстовое сообщение в поле. После этого его сообщение появится в ленте в обратном хронологическом порядке. Пользователи могут ставить лайки на записи и комментировать их. Для этого необходимо ввести свой комментарий в поле под записью и нажать кнопку Написать комментарий. Для просмотра оставленных комментариев - нажать кнопку Посмотреть комментарии.

#### 3. API сервера и хореография
![Добавление заметки](https://user-images.githubusercontent.com/90519017/209437984-9f747e57-149c-48e3-b5ae-14219ec54a8b.png)


![Реакция](https://user-images.githubusercontent.com/90519017/209437994-86cf8c43-f234-4a48-80ed-6e4626a5a715.png)


#### 4. Структура базы данных

 Таблица *post*
| Название | Тип | NULL | Описание |
| :------: | :------: | :------: | :------: |
| **id** | INT  | NO | Автоматический идентификатор поста |
| **login** | TEXT | NO | Логин пользователя |
| **text** | TEXT | NO | Текст заметки |
| **date** | INT | NO | Дата создания поста |
| **like** | INT | NO | Количество лайков |

Таблица *likes*
| Название | Тип | NULL | Описание |
| :------: | :------: | :------: | :------: |
| **id** | INT  | NO | Идентификатор комментария |
| **post_id** | INT  | NO | Идентификатор поста |


#### 5. Алгоритмы
![Алгоритм](https://user-images.githubusercontent.com/90519017/209438095-cd5a71fb-72ff-4834-bd4d-a3a8860aed1f.png)


#### 6. HTTP запрос/ответ
**Запрос**  
Request URL: http://localhost/
Request Method: GET
Status Code: 200 OK
Remote Address: [::1]:80
Referrer Policy: strict-origin-when-cross-origin
Connection: Keep-Alive
Content-Length: 5559
Content-Type: text/html; charset=UTF-8
Date: Sat, 24 Dec 2022 13:39:40 GMT
Keep-Alive: timeout=5, max=98
Server: Apache/2.4.54 (Win64) OpenSSL/1.1.1o PHP/7.4.30
X-Powered-By: PHP/7.4.30
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Accept-Encoding: gzip, deflate, br
Accept-Language: ru,en;q=0.9
Cache-Control: max-age=0
Connection: keep-alive
Host: localhost
sec-ch-ua: "Chromium";v="106", "Yandex";v="22", "Not;A=Brand";v="99"
sec-ch-ua-mobile: ?0
sec-ch-ua-platform: "Windows"
Sec-Fetch-Dest: document
Sec-Fetch-Mode: navigate
Sec-Fetch-Site: none
Sec-Fetch-User: ?1
Upgrade-Insecure-Requests: 1

**Ответ**
Connection: Keep-Alive
Content-Length: 6271
Content-Type: text/html; charset=UTF-8
Date: Sat, 24 Dec 2022 13:42:26 GMT
Keep-Alive: timeout=5, max=99
Server: Apache/2.4.54 (Win64) OpenSSL/1.1.1o PHP/7.4.30

**Добавление комментариев: **
```
include_once("template/settings.php");

if(isset($_GET['comment']))
{
    if(isset($_GET['com']))
    {
        $post_id = $_GET['com'];
        $comment = $_GET['comment'];
        $id = 0;
        $sql = mysqli_query($db, "INSERT INTO `comments` (`id`, `post_id`, `message`) VALUES ('".$id."', '".$post_id."', '".$comment."')");
    }
}
header('Location: index.php');
exit();

```
**Код получение комментария и записи его в базу данных:**
```
if (isset($_POST["text"])) {	
		$text = $_POST["text"];
		$time = time();
		$post_id = $_POST["post_id"];
		$page = $_POST["forum_page"];
	
		if (strlen($text) > 1000) {
			header("Location: http://localhost/forum.php?message=Слишком длинный текст");
			exit();
		}
		
		if (strlen($text) < 4) {
			header("Location: http://localhost/forum.php?message=Слишком короткий текст");
			exit();
		}
	
		$sql = "INSERT INTO `comments` (`id`, `text`, `post_id`, `time`) VALUES (NULL, '".$text."', '".$post_id."', '".$time."');";
		$link = mysqli_connect("localhost", "root", "", "posts");
		mysqli_set_charset($link, "utf8");
		$res = mysqli_query($link, $sql);
	
		header("Location: http://localhost/forum.php?page=".$page);
		exit();
	}

```

**Код раскрывающихся комментариев:**
```
<script> 
	function show_comments(id){
		let c = document.getElementById("c"+id);
		c.removeAttribute("hidden");
		
		let b = document.getElementById("b"+id);
		b.textContent = "Скрыть комментарии";
		
		b.setAttribute("onClick", "hide_comments('"+id+"')");
	}
	
	function hide_comments(id) {
		let c = document.getElementById("c"+id);
		c.setAttribute("hidden", true);
		
		let b = document.getElementById("b"+id);
		b.textContent = "Показать комментарии";
		
		b.setAttribute("onClick", "show_comments('"+id+"')");
	}
</script>
```
