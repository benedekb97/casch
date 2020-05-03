channel.bind('message', function(data){
    let messages = $('#chat-messages');
    let sent_by = data.message.sent_by;
    let message = data.message.message;
    let sent_at = data.message.sent_at;
    let spectator = data.message.spectator;
    let user_id = data.message.user_id;

    let current_html = messages.html();

    let add_text = `<span class="chat-message`;

    if(user_id == $('#user_id').val()) {
        add_text += ` player-message`;
    }

    if(spectator) {
        add_text += ` spectator`;
    }

    add_text += `" data-toggle="tooltip" data-placement="left" title="${sent_at}">${sent_by}: ${message}</span>`;

    messages.html(current_html + add_text);

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    $('#chat-messages').animate({ scrollTop: $('#chat-messages').prop("scrollHeight")}, 500);

    if(!show){
        unread++;

        $('#chat-title').html(`Chat&nbsp;&nbsp;<span style="border-radius:3px; background-color:red; color:white; padding:2px;">${unread}</span>`);
    }
});

$(document).ready(function(){
    let html = ``;
    $.ajax({
        url: $('#chat-messages-url').val(),
        type: "POST",
        dataType: "json",
        data: {
            _token: $('#_token').val()
        },
        success: function(data){
            let messages = data.messages;
            let new_messages = [];
            let iter = 10;
            messages.forEach(function(message){
                new_messages[iter] = message;
                iter--;
            });

            new_messages.forEach(function(element){
                html += `<span class="chat-message`;
                if(element.user_id == $('#user_id').val()){
                    html += ` player-message`;
                }
                html += `" data-toggle="tooltip" data-placement="left" title="${element.sent_at}">${element.sent_by}: ${element.message}</span>`;
            });

            $('#chat-messages').html(html);

            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            });

            $('#chat-messages').animate({ scrollTop: $('#chat-messages').prop("scrollHeight")}, 500);
        },
        error: function(data){
            console.log(data);
        }
    });
});

function sendText(){

    let chat_text = $('#chat-text');
    if(chat_text.val() == '')
        return;

    $.ajax({
        url: $('#chat-url').val(),
        type: "POST",
        dataType: "json",
        data: {
            _token: $('#_token').val(),
            message: chat_text.val()
        },
        success: function(e){
        },
        error: function(e){
            console.log(e);
        }
    });

    chat_text.val('');
}

$('#chat-send').on('click', sendText);

$('#chat-text').keypress(function(e){
    if(e.which == 13) {
        sendText();
        return false;
    }
});

let show = false;
let unread = 0;

$('#chat-title').on('click', function(){
    if(show){
        $('#chat-box').animate({ bottom: '-250px'}, 250);
        $('#chat-title').css('background', 'rgba(255,255,255,0.05)');
        show = false;
    }else{
        $('#chat-box').animate({ bottom: '0'}, 250);
        $('#chat-title').css('background', 'rgba(255,255,255,0.15)');
        unread = 0;
        $('#chat-title').html('Chat');
        show = true;
    }
});
