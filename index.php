<?php
require('db.php');
/**
* Adding News for Demo request by applying check on addnews POST parameter
**/
if(isset($_POST['addnews'])){
	$news = filter_input(INPUT_POST, 'news', FILTER_SANITIZE_SPECIAL_CHARS);
	$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
	$sql = "INSERT INTO news (description, name, date) VALUES ('".$news."', '".$name."', '".date('Y-m-d H:i:s')."')";
	mysql_query($sql);
}
/**
* Preparing and getting response for latest feed items.
**/
if(isset($_POST['latest_news_time'])){
	$sql = "SELECT * FROM news WHERE date > '".$_POST['latest_news_time']."' ORDER BY date DESC";
	$resource = mysql_query($sql);
	$current_time = $_POST['latest_news_time'];
	$item = mysql_fetch_assoc($resource);
	$last_news_time = $item['date'];
	while ($last_news_time < $current_time) {
		usleep(1000); //giving some rest to CPU
		$resource = mysql_query($sql);
		$item = mysql_fetch_assoc($resource);
		$last_news_time = $item['date'];
	}
	?>
	<li id="<?php echo $item['date'] ?>">
		<span class="feedtext"><?php echo $item['description'] ?> was added by <b><?php echo $item['name'] ?></b></span>
	</li>
	<?php
	exit;
}
/**
* Getting news Items and preparing sql query with respect to request
**/
$sql = "SELECT * FROM news ORDER BY date DESC LIMIT 0, 10";
if(isset($_POST['last_time'])){
	$sql = "SELECT * FROM news WHERE date < '".$_POST['last_time']."' ORDER BY date DESC LIMIT 0, 10";
}
$resource = mysql_query($sql);
$news = array();
while($row = mysql_fetch_assoc($resource)){
	$news[] = $row;
}

?>
<html>
<head>
<title>:: News Feed ::</title>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js" type="text/javascript"></script>
	<script src="js/scroll-pagination.js" type="text/javascript"></script>
	<script src="js/slimScroll.js" type="text/javascript"></script>
	<link href="styles.css" rel="stylesheet" type="text/css" />
</head>
<script type="text/javascript">
$(function(){
	/**
	* Integrating slim scroll
	**/
	$("#feeds ul").slimScroll({
        height: '520px'
    });
	/**
	* Integrating Scroll Pagination
	**/
	var feeds = $("#feeds ul");
	var last_time = feeds.children().last().attr('id');
    feeds.scrollFeedPagination({
        'contentPage': 'index.php',
        'contentData': {
            'last_time' : last_time
        },
        'scrollTarget': feeds, 
        'beforeLoad': function(){
            feeds.parents('#feeds').find('.loading').fadeIn();
        },
        'afterLoad': function(elementsLoaded){
            last_time = feeds.children().last().attr('id');
            feeds.scrollFeedPagination.defaults.contentData.last_time = last_time;
            feeds.parents('#feeds').find('.loading').fadeOut();
            var i = 1;
            $(elementsLoaded).fadeInWithDelay();
        }
    });
    $.fn.fadeInWithDelay = function(){
        var delay = 0;
        return this.each(function(){
            $(this).delay(delay).animate({
                opacity:1
            }, 200);
            delay += 100;
        });
    };
	//calling the function to update news feed
    setTimeout('updateFeed()', 1000);
});
/**
* Function to update the news feed
**/
function updateFeed(){
		var id = 0;
		id = $('#feeds li :first').attr('id');
        $.ajax({
            'url' : 'index.php',
            'type' : 'POST',
            'data' : {
                'latest_news_time' : id  
            },
            success : function(data){
				setTimeout('updateFeed()', 1000);
				if(id != 0){
                	$(data).prependTo("#feeds ul");
				}
            }
        }) 
	}
</script>
<p><a href="http://feeds.feedburner.com/Techknowlogists"><img src="http://feeds.feedburner.com/~fc/Techknowlogists?bg=99CCFF&amp;fg=444444&amp;anim=0" height="26" width="88" style="border:0" alt="" /></a></p>
<body>
	<div class="main_container">
		<div class="feeds_container">
			<h3 class="feed_head">News Feed</h3>
			<div id="feeds" class="feeds">
				<ul>
					<?php foreach($news as $item): ?>
					<li id="<?php echo $item['date'] ?>">
						<span class="feedtext"><?php echo $item['description'] ?> was added by <b><?php echo $item['name'] ?></b></span>
					</li>
					<?php endforeach; ?>
				</ul>
				<div class="loading">
					<img src="images/loading_transparent.gif"  alt=""/>
				</div>
			</div>
		</div>
		<div class="form">
			<form action="" id="add-news-form" method="post">
				Name : <input name="name" id="name" type="text" /><br>
				News : <input name="news" id="news" type="text" /><br>
				<input type="button" onclick="addNews()" value="Add News" />
		</div><br><br style="clear: both">
	</div>
</body>
</html>
<script>
/**
* Function to add news
**/
function addNews(){
	var validation = "";
	var values = {};
	$.each($('#add-news-form').serializeArray(), function(i, field) {
        if((field.value == 0)){
            validation = "false";
        }
        values[field.name] = field.value;
    });
	if(validation == "false"){
		alert("Name and News are required Values");
		return false;
	}
	values['addnews'] = '';
	$.ajax({
        'url' : '',
        'type' : 'POST',
        'data' : values,
        success : function(data){
			
        }
    }) 
}
</script>