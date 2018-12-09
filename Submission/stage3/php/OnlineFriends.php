<?php

class OnlineFriends {
  
  private $db;
  
  public function __construct() {
    $this->db = new mysqli('localhost', 'tblazek', 'goirish', 'tblazek');
  }
    
  /**
   * Update the last login in checkin time
   */
  public function updateLastCheckin($username) {
    $query = "UPDATE Players SET last_active = NOW(), is_loggedin = 1 ". "WHERE username='" . $username . "'";
    $result = $this->db->query($query);
  }
  
  /**
   * Get the list of user's friends
   * @param type $userId
   */
  public function getUserFriends($username) {
    $query = "SELECT * FROM Invitations WHERE (inviter = '" . $username . "' OR receiver = '" . $username . "') AND accepted = 'yes'";
    $result = $this->db->query($query);
    $friends = array();
    if ($result) {
      while($row = $result->fetch_assoc()) {
          $friends[] = $row;
      }
      return $friends;
    }
  }
  
  /**
   * Get's list of online friends for the current user.
   * @param int $userId
   */
  public function getOnlineFriends($username) {
    $friends = $this->getUserFriends($username);
    
    $friend_uname = '(';
    
    if (!empty($friends)) {
        foreach ($friends as $friend) {
            if ($friend['inviter'] !== $username) {
                $friend_uname .= '"' . $friend['inviter'] . '",';
            }
            if ($friend['receiver'] !== $username) {
                $friend_uname .= '"' . $friend['receiver'] . '",';
            }
        }
    } else {
      return array();
    }
    // Gather the list of friends id's.
    $friend_uname = substr($friend_uname, 0, (strlen($friend_uname) - 1)) . ')';
    
    $query = 'SELECT * FROM Players ' . 'WHERE TIME_TO_SEC(TIMEDIFF(NOW(), Players.last_active)) <= 180 ' . 'AND Players.is_loggedin = 1 ' . "AND Players.username IN {$friend_uname}";
    $result = $this->db->query($query);
    
    if ($result) {
      $onlineFriends = array();
      while($row = $result->fetch_assoc()) {
        $onlineFriends[] = $row;
      }
      
      return $onlineFriends;
    }
    
    return array();
  }
  
  /**
   * Logout the user
   * @param type $userId
   */
  public function logout($username) {
    $query = 'UPDATE `Players` SET `Players`.`is_loggedin` = 0 ' . 
              'WHERE `Players`.`username` = "' . $username . '"';
    $this->db->query($query);
  }

}
