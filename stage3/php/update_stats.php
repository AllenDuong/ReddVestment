<?php
session_start();

$online_friends = array();

// Check if the user is logged in.
if (isset($_SESSION['username']) && isset($_COOKIE['username']) &&
    $_SESSION['username'] === $_COOKIE['username'] && 
    isset($_SESSION['username']) && $_SESSION['is_loggedin'] === true) {
  
  include_once __DIR__ . '/OnlineFriends.php';

  $app = new OnlineFriends();
  // Get the list of online friends for the current user.
  $username = $_SESSION['username'];
  $online_friends = $app->getOnlineFriends($username);
  $html = '';
  $link = mysqli_connect('localhost', 'tblazek', 'goirish') or die('Database Connection Error');
  mysqli_select_db($link, 'tblazek');
  if (!empty($online_friends)) {
      foreach ($online_friends as $friend) {
          $friend_uname = $friend['username'];
        $html .= '<li class="list-group-item">' .
              '<span style="color:rgb(66,183,42);">• </span>' .
              $friend['name'] . " (" . $friend['username'] . ")   " . "<select name='listsub' id='listsub" . $friend['username'] . "'>";
          //$query = "SELECT d.subreddit FROM (SELECT DISTINCT Posts.subreddit as subreddit FROM Posts LEFT JOIN (SELECT Posts.subreddit, count(*) as num2, sum(if(a.subreddit is not null, 1, 0)) as num FROM Posts LEFT JOIN (SELECT subreddit, MAX(time_chosen) as recent FROM Investments WHERE username='$username' GROUP BY subreddit) a ON Posts.subreddit=a.subreddit WHERE a.subreddit IS NULL OR (Posts.created > a.recent) GROUP BY Posts.subreddit) b ON Posts.subreddit=b.subreddit WHERE b.num>=10 OR (b.num=0 AND b.num2>=10) ORDER BY subreddit) c, (SELECT DISTINCT Posts.subreddit as subreddit FROM Posts LEFT JOIN (SELECT Posts.subreddit, count(*) as num2, sum(if(a.subreddit is not null, 1, 0)) as num FROM Posts LEFT JOIN (SELECT subreddit, MAX(time_chosen) as recent FROM Investments WHERE username='$friend_uname' GROUP BY subreddit) a ON Posts.subreddit=a.subreddit WHERE a.subreddit IS NULL OR (Posts.created > a.recent) GROUP BY Posts.subreddit) b ON Posts.subreddit=b.subreddit WHERE b.num>=10 OR (b.num=0 AND b.num2>=10) ORDER BY subreddit) d WHERE c.subreddit = d.subreddit";
            $query = "SELECT X.subreddit FROM (SELECT DISTINCT Posts.subreddit FROM Posts LEFT JOIN (SELECT subreddit, MAX(time_chosen) as max FROM Investments WHERE username='$friend_uname' GROUP BY subreddit HAVING max > (UNIX_TIMESTAMP() - (12*60*60))) a ON Posts.subreddit=a.subreddit WHERE a.subreddit IS NULL) X, (SELECT DISTINCT Posts.subreddit FROM Posts LEFT JOIN (SELECT subreddit, MAX(time_chosen) as max FROM Investments WHERE username='$username' GROUP BY subreddit HAVING max> (UNIX_TIMESTAMP() - (12*60*60))) b on Posts.subreddit=b.subreddit WHERE b.subreddit IS NULL) Y WHERE X.subreddit=Y.subreddit";
        $result = mysqli_query($link, $query) or die('Query Failed: ' .mysql_error());
        while ($tuple = mysqli_fetch_array($result, MYSQL_ASSOC)) {
            $subreddit = $tuple["subreddit"];
            $html .= "<option value='$subreddit'>$subreddit</option>";
        }
        $html .= "</select>  <button class='bn btn-info' id='challengeButton" . $friend['username'] . "' onClick=\"sendGameInvite('" . $username . "','".$friend['username']."')\">Challenge</button></li>";
    }
    mysqli_free_result($result);
  }
    mysqli_close($link);
  // Update the user's last active time.
  $app->updateLastCheckin($_SESSION['username']); 
}

// Send response to browser.
if ($html !== '') echo $html;
