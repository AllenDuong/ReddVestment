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
  // Finds the online friends
  if (!empty($online_friends)) {
      // Creates a list group for each of the friend in the online friends
      foreach ($online_friends as $friend) { // Sends a query for each online friend to see which subreddits they can both invest in
        $friend_uname = $friend['username'];
        $html .= '<li class="list-group-item">' .
              '<span style="color:rgb(66,183,42);">• </span>' .
              $friend['name'] . " (" . $friend['username'] . ")   " . "<select name='listsub' id='listsub" . $friend['username'] . "'>";
          $query = "SELECT X.subreddit FROM (SELECT DISTINCT Posts.subreddit FROM Posts LEFT JOIN ((SELECT subreddit, MAX(time_chosen) as max FROM Investments WHERE username='$username' GROUP BY subreddit HAVING max > (UNIX_TIMESTAMP() - (12*60*60))) UNION (SELECT subreddit, max(time_created) as max FROM Games WHERE (player1='$username' OR player2='$username') GROUP BY subreddit HAVING max > (UNIX_TIMESTAMP() - (12*60*60)))) u ON u.subreddit=Posts.subreddit WHERE u.subreddit IS NULL) X, (SELECT DISTINCT Posts.subreddit FROM Posts LEFT JOIN ((SELECT subreddit, MAX(time_chosen) as max FROM Investments WHERE username='$username' GROUP BY subreddit HAVING max > (UNIX_TIMESTAMP() - (12*60*60))) UNION (SELECT subreddit, max(time_created) as max FROM Games WHERE (player1='$username' OR player2='$username') GROUP BY subreddit HAVING max > (UNIX_TIMESTAMP() - (12*60*60)))) u ON u.subreddit=Posts.subreddit WHERE u.subreddit IS NULL) Y WHERE X.subreddit=Y.subreddit";


        $result = mysqli_query($link, $query) or die('Query Failed: ' .mysql_error());
        while ($tuple = mysqli_fetch_array($result, MYSQL_ASSOC)) {
            $subreddit = $tuple["subreddit"];
            $html .= "<option value='$subreddit'>$subreddit</option>"; // Adds the options for each of the subreddits
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
