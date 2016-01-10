var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
var form = $('#micro-chat-form');
var input = $('#micro-chat-input');
var container = $('#micro-chat-container');
var content = $('#micro-chat-content');
var notification = $('#micro-chat-notification');
var spinner = $('#micro-chat-spinner');
var usersOpenToggle = $('#micro-chat-users-open-toggle');
var usersCloseToggle = $('#micro-chat-users-close-toggle');
var usersListContainer = $('#micro-chat-users-list-container');
var usersList = $('#micro-chat-users-list');
var usersNumber = $('.micro-chat-users-number');
var usersPlurial = $('#micro-chat-users-plurial');

$(document).ready(function(){
    /**
     * Get Messages
     */
    var getMessages = setInterval(function(){
        $.ajax({
            method: 'GET',
            url: messagesPath,
            success: function(datas){
                spinner.hide();
                content.html(datas.render);
                container.removeClass('hidden');
            },
            error: function(){
                console.log('ERROR');
            }
        });
    }, 3000);

    /**
     * Get Users
     */
    var getUsers = setInterval(function(){
        $.ajax({
            method: 'GET',
            url: usersPath,
            success: function(datas){
                usersNumber.html(datas.number);

                if(datas.number > 0){
                    usersList.html(datas.render);
                } else {
                    usersList.html('');
                }

                if(datas.number > 1){
                    usersPlurial.html('s');
                } else {
                    usersPlurial.html('');
                }
            },
            error: function(){
                console.log('ERROR');
            }
        });
    }, 3000);

    /**
     * Form submit handler
     */
    form.submit(function(e){
        if(input.val() !== ''){
            var messageContent = input.val();
            input.val('');

            $.ajax({
                method: 'POST',
                url: sendPath,
                data: {
                    content: messageContent
                },
                success: function(datas){
                    content.html(datas.render);
                },
                error: function(){
                    console.log('ERROR');
                }
            });
        }

        e.preventDefault();
    });

    /**
     * Users list handlers
     */
    usersOpenToggle.click(function(){
        var animationIn = 'animated slideInRight';

        $(this).hide();
        usersListContainer.addClass(animationIn).show().one(animationEnd, function(){
            $(this).removeClass(animationIn);
        });
    });

    usersCloseToggle.click(function(){
        var animationOut = 'animated slideOutRight';

        usersListContainer.addClass(animationOut).one(animationEnd, function(){
            $(this).removeClass(animationOut).hide();
            usersOpenToggle.show();
        });
    });
});

/**
 * Refresh user connexion
 */
function userUpdateConnexionLink()
{
    console.log('Reconnexion');

    $.ajax({
        method: 'GET',
        url: updateUserPath,
        success: function(datas){
            content.html(datas.render);
        },
        error: function(){
            console.log('ERROR');
        }
    });
}

function washConversation()
{
    $.ajax({
        method: 'GET',
        url: washPath,
        success: function(datas){
            addNotification(datas.render);
        },
        error: function(){
            console.log('ERROR');
        }
    });
}

function addNotification(notifFromServer)
{
    var animationIn = 'animated slideInLeft';
    var animationOut = 'animated slideOutRight';

    var notifRender = $(notifFromServer).addClass(animationIn).one(animationEnd,  function(){
        var notif = $(this);
        notif.removeClass(animationIn);

        setTimeout(function(){
            notif.addClass(animationOut).one(animationEnd,  function(){
                notif.remove();
            });
        }, 5000);
    });

    notification.append(notifRender);
}