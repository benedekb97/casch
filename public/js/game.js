Pusher.logToConsole = true;

var pusher = new Pusher('c294b79228fa69e9f4f5', {
    cluster: 'eu',
    forceTLS: true
});

let slug = $('#slug').val();

var channel = pusher.subscribe('game-' + slug);
channel.bind('join-game', function(data) {
    console.log(data);
    let html = $('#user-list').html();
    html += `<li class="list-group-item" id="player_` + data.message.id + `">` + data.message.name + "</li>";

    $('#user-list').html(html);
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
});

channel.bind('leave-game', function(data) {
    let player_id = data.message.id;
    let new_host = data.new_host;
    let user_id = $('#user_id').val();

    if(new_host !== null) {
        let host_html = $('#player_' + new_host).html() + ` <i data-toggle="tooltip" title="Játékvezető" class="fa fa-crown"></i>`;
        $('#player_' + new_host).html(host_html);

        if(new_host == $('#player_id').val()) {
            $('#rounds').removeAttr('readonly');
            $('#start-button').css('display','block');
            $('#start-button').html(`<button style="margin-top:10px;" type="button" class="btn btn-block btn-primary" id="start">Indítás</button>`);
        }else{
            $('#start-button').css('display','none');
            $('#rounds').attr('readonly','readonly');
        }

    }
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

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
            console.log(e);
        },
        error:function(e){
            console.log(e);
        }
    })
});
