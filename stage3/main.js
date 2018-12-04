// index.php
function toggleAvailableButton(){
    if($("#userName").val() != "" && $("#password").val() != ""){
        document.getElementById("loginButton").disabled = false
    }else{
        document.getElementById("loginButton").disabled = true
    }
}

// register.html
function toggleAvailableButtonReg(){
    if($("#name").val() != "" && $("#userName").val() != "" && $("#password").val() != "" && $("#password").val() == $("#passwordConf").val()){
        document.getElementById("registerButton").disabled = false
    }else{
        document.getElementById("registerButton").disabled = true
    }
}

// settings.php
function toggleAvailableButtonSettings(){
    if($("#name").val() != "" || ($("#password").val() != "" && $("#password").val() == $("#passwordConf").val())){
        document.getElementById("registerButton").disabled = false
    }else{
        document.getElementById("registerButton").disabled = true
    }
}

// settings.php
function changeButtonClick(){
    $.post("php/changeSettings.php",
        {
            request: "updateUserDB",
            name: $("#name").val(),
            oldPassword: $("#oldPassword").val(),
            password: $("#password").val(),
            passwordConf: $("#passwordConf").val()
        },
        function(data){
            if(data == "change=success"){
                alert("You have successfully changed your information!")
                window.location.replace("home.php")
                return true
            }else{
                alert(data)
                return false
            }
        }
    )
}

// index.php
function registerButtonClick(){
    $.post("php/login.php",
        {
            request: "register",
            userName: $("#userName").val(),
            password: $("#password").val(),
            name: $("#name").val()
        },
        function(data){
            if(data == "reg=success"){
                alert("You have successfully registered!")
                window.location.replace("index.php")
                return true
            }else if(data == "reg=userExists"){
                alert("This username is taken. Try another.")
                return true
            }else{
                alert(data)
                return false
            }
        }
    )
}

// Send login info to php, which will query the database
// index.php
function loginButtonClick(){
    $.post("php/login.php",
        {
            request: "login",
            userName: $("#userName").val(),
            password: $("#password").val()
        },
        function(data){
            if(data == "auth=success"){
                alert("login success")
                window.location.replace("home.php")
                return true
            }else if(data == "auth=failure"){
                alert("login failure")
                return false
            }else{
                alert(data)
                return false
            }
        });
}

// Function to Delete User Account
// settings.php
function deleteAccount(){
    $.post("php/DELETE.php",
        {
            request: "deleteUser"
        },
        function(data){
            if(data == "change=success"){
                alert("You have deleted your account!")
                window.location.replace("index.php")
                return true
            }else{
                alert(data)
                return false
            }
        }
    )
}

// game.php
function updateURLsubreddit(subreddit){
    $("#subreddit_load").load("php/gameLoad.php?subreddit=" + subreddit);
    if (subreddit.value != ""){
        $("#game_info").show()
    } else {
        $("#game_info").hide()
    }
}

//game.php
function readChoiceClick(gamemode){
    $.post("php/gameLoad.php",
        {
            request: "chooseRedPost",
            subreddit: $("#subselect").val(),
            redpost_id: document.getElementsByClassName("active")[0].value,
            mode: gamemode
        },
        function(data){
            if(data == "auth=success"){
                alert("You have successfully invested in a post!")
                window.location.reload()
                return true
            }else if(data == "auth=failure"){
                alert("Error investing in post")
                window.location.reload()
                return false
            }else{
                alert(data)
                window.location.reload()
                return false
            }
        });
}

// friends.php
function searchForNewFriend(){
    if (event.keyCode == 13) { //pushes enter
        $.post("php/friend_search.php",
            {
                request: "sendInvitation",
                friend_uname: $("#friends").val(),
            },
            function(data){
                if(data == "search=success"){
                    alert("Friend request sent")
                    window.location.reload()
                    return true
                }else if(data == "search=failure"){
                    alert("Failed sending friend request")
                    window.location.reload()
                    return false
                }else{
                    alert(data)
                    window.location.reload()
                    return false
                }
            }
        );
    }
}
// friends.php
$(function() {
    function updateOnlineFriends() {
        $.ajax({
            url: 'php/update_stats.php',
            dataType: 'html',
            success: function(data, status) {
                if (data.length > 0) {
                    $('#online_friends').html(data);
                } else {
                    $('#online_friends').html('<li class="list-group-item">No friends online.</li>');
                }
            }
        });
    }
    function updateGameInvites(){
        $.post( "php/game_invite.php",
            {
                request:"updateInvite"
            }
        );
    }
    function lookForGameInvites(){
        $.post("php/game_invite.php",
            {
                request:"lookForInvite"
            },
            function(data){
                if (data!="none"){
                    result = data.split(" ");
                    var n = (Math.round(Date.now()/1000) - parseInt(result[3])); // how much time there is left to accept
                    var left = 15 - n;
                    if (result[2] == "received"){
                        document.getElementById("game-invite-body").innerHTML = result[0] + " would like to play you in " + result[1];
                        document.getElementById("gameInviteDecline").innerHTML = "Decline";
                        document.getElementById("gameInviteDecline").style.visibility = 'visible';
                        document.getElementById("gameInviteDecline").setAttribute('onClick', "sendGameInviteResponse('" + result[0] + "','" + result[1] + "', 0)");
                        document.getElementById("gameInviteAccept").style.visibility = 'visible';
                        document.getElementById("gameInviteAccept").setAttribute('onClick', "sendGameInviteResponse('" + result[0] + "','" + result[1] + "', 2)");
                    } else {
                        document.getElementById("game-invite-body").innerHTML = "Waiting for response...";
                        document.getElementById("gameInviteDecline").style.visibility = 'hidden';
                        document.getElementById("gameInviteAccept").style.visibility = 'hidden';
                    }
                    document.getElementById("progress-bar-modal").innerHTML = left + " secs";
                    document.getElementById("progress-bar-modal").style.width = Math.round((n/15)*100) + "%";
                    $('#gameInviteModal').modal('show');
                } else {
                    $('#gameInviteModal').modal('hide');
                }
            }
        );
    }
    function loadVsPage() {
        console.log(document.getElementById("gameInviteModal").style.display);
        if (document.getElementById("gameInviteModal").style.display == "block"){
            $.post("php/game_invite.php",
                {
                    request: "lookForAccept"
                },
                function(data){
                    if (data=="accepted"){
                        window.location.replace("vsGame.php");
                    }
                }
            );
        }
    }
    // Update friends list every 12 seconds
    if (window.location.pathname == "/cse30246/rawtoast/stage3/friends.php"){
        $(document).ready(function() {updateOnlineFriends()});
        setInterval(updateOnlineFriends, 10000);
        setInterval(lookForGameInvites, 2000);
        setInterval(loadVsPage, 2000);
    }
    });

// friends.php
function sendGameInvite(uname, f_uname) {
    var sub = document.getElementById("listsub" + f_uname).value;
    $.post("php/game_invite.php",
        {
            request: "sendGameInvite",
            user: uname,
            friend: f_uname,
            subreddit: sub
        },
        function(data) {
            if (data != "send=success") {
                alert(data)
            }
        }
    );
}

// friends.php
function sendGameInviteResponse(friend_uname, sub, val) {
    $.post("php/game_invite.php",
        {
            request: "updateInvite",
            friend: friend_uname,
            subreddit: sub,
            accepted: val
        },
        function(data) {
            if (data != "success") {
                alert("<" + data + ">")
            }
        }
    );
}

// friends.php
function requestResponse(i_uname, r_uname, response) {
    $.post("php/friend_search.php",
        {
            request: "requestResponse",
            inviter: i_uname,
            receiver: r_uname,
            accepted: response
        },
        function(data){
            if(data != "success") {
                alert(data)
            }
            window.location.reload()
        }
    );
}

// vsGame.php
function readChoiceClickVs(){
    $.post("php/gameLoad.php",
        {
            request: "chooseRedPost",
            redpost_id: document.getElementsByClassName("active")[0].value,
            mode: "vs"
        },
        function (data) {
            //console.log(data);
            if (data == "auth=success"){
                window.location.replace("friends.php");
            }
        }
    );
}