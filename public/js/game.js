var pusher = new Pusher($('#pusher').val(), {
    cluster: 'eu',
    forceTLS: true
});

let slug = $('#slug').val();

var channel = pusher.subscribe('game-' + slug);
channel.bind('join-game', function(data) {
    let html = $('#user-list').html();
    let name;
    if(data.message.nickname != null) {
        name = data.message.nickname;
    }else{
        name = data.message.name;
    }
    html += `<li class="list-group-item" id="player_${data.message.id}">${name}&nbsp;<i id="ready-${data.message.id}" class="fa fa-check" style="display:none"></i></li>`;

    $('#user-list').html(html);
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
    $('#start').css('display','none');
});

channel.bind('leave-game', function(data) {
    let player_id = parseInt(data.message.id);
    let new_host = data.new_host;
    let user_id = $('#user_id').val();

    let current_player_id = parseInt($('#player_id').val());

    console.log(player_id , current_player_id);
    if(player_id == current_player_id){
        $('#player_' + player_id).remove();
    }else{
        if(new_host !== null){
            location.reload();
        }
    }

    $('#player_' + player_id).remove();

});

channel.bind('edit-game', function(data) {
    let rounds = data.message.rounds;
    if(data.host_id != $('#user_id').val()){
        $('#rounds').val(rounds);
    }
});

channel.bind('start-game', function(data){
    window.location = $('#game_url').val();
});

channel.bind('start-load', function(){
    $('#waiting-for-game').modal('show');
});

channel.bind('player-ready', function(data){
    let user_id = data.message.id;
    let everyone_ready = data.message.everyone_ready;
    let ready_icon = $('#ready-' + user_id);
    if(ready_icon.css('display') == 'inline'){
        ready_icon.css('display','none');
    }else{
        ready_icon.css('display','inline');
    }

    if(everyone_ready) {
        $('#start').css('display','block');
    }else{
        $('#start').css('display','none');
    }
});

channel.bind('finished-game', function(data){
    window.location = data.message;
});

channel.bind('change-deck', function(data){
    console.log(data);
    $('#deck').val(data.message);
});

$('#ready').on('click', function(){
    $.ajax({
        url: $('#ready_url').val(),
        type: "POST",
        dataType: "json",
        data: {
            _token: $('#_token').val()
        },
        success: function(e){
            console.log(e);
        }
    });
    if($('#ready').html() == "Ready") {
        $('#ready').html("Unready");
        $('#ready').removeClass('btn-primary');
        $('#ready').addClass('btn-default');
    }else{
        $('#ready').html('Ready');
        $('#ready').removeClass('btn-default');
        $('#ready').addClass('btn-primary');
    }
});

$('#game-slug').on('click', function(e){
    e.target.select();
    document.execCommand('copy');
});

$(document).ready(function(){
    $('#rounds').on('keyup', function(){
        $.ajax({
            url: $('#change_url').val(),
            type: 'POST',
            dataType: 'json',
            data: {
                _token: $('#_token').val(),
                rounds: $('#rounds').val()
            }
        });
    });
});

$('#start').on('click', function(){
    $.ajax({
        url: $('#start_game_url').val(),
        type: 'POST',
        dataType: 'json',
        data: {
            _token: $('#_token').val()
        },
        success:function(e){
            if(e.success == true){
                $('#start-button').css('display','none');
            }else if('deck' in e){
                $('#deck-alert').css('display','block');
                console.log('no deck selected');
            }
        },
        error:function(e){
            console.log(e);
        }
    });
});

$('#deck').on('change', function(element){
    let deck_id = $('#deck').val();

    $.ajax({
        url: $('#deck_change_url').val(),
        dataType: 'json',
        type: 'POST',
        data:{
            _token: $('#_token').val(),
            deck: deck_id
        },
        success: function(e){
            console.log(e);
        },
        error: function(e){
            console.log(e);
        }
    });
});
