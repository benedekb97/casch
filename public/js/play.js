$(document).ready(function(){
    $.ajax({
        url: $('#get_data_url').val(),
        type: "POST",
        dataType: "json",
        data:{
            _token: $('#_token').val()
        },
        success:function(e) {
            let black_card_div = $('#black-card');
            let white_cards_div = $('#white-cards');
            let players_div = $('#players');
            let answers_div = $('#answers');
            let host_user_id = parseInt(e.host_user_id);
            let user_id = parseInt($('#user_id').val());

            let white_cards_html = ``;
            let players_html = `<h5 class="card-title">Játékosok</h5><ul class="list-group">`;

            // Show black card data
            let black_card_text = JSON.parse(e.black_card.text).join(" ____ ");

            black_card_div.html(
                `<div class="card-body"><h5 class="card-title">` + black_card_text + `</h5></div>`
            );

            // Show white cards
            if(host_user_id == user_id) {
                white_cards_html += `<h1 style="text-align:center;">Örülj, hogy csak online vagy koronás</h1>`;
            }else{
                e.cards.forEach(function(element){
                    let card_text = JSON.parse(element.text).join("");
                    if(host_user_id != user_id && $.inArray(user_id,e.players_played) === -1){
                        white_cards_html += `<div class="col-xl-3" id="white-card-${element.id}"><a class="card-link" href="#" onclick="addAnswer(${element.id},'${card_text}')"><div class="card card-white"><div class="card-body">${card_text}<p></p></div></div></a></div>`;
                    }else{
                        white_cards_html += `<div class="col-xl-3"><div class="card card-white"><div class="card-body"><p>${card_text}</p></div></div></div>`;
                    }
                });
            }
            white_cards_div.html(white_cards_html);

            // Show players
            e.players.forEach(function(element){
                if(element.id===host_user_id){
                    players_html += `<li style="border-bottom:none; margin-bottom:0; padding-bottom:0;" class="list-group-item" id="player-${element.id}">${element.name} <i data-toggle="tooltip" title="Ő választ nyertest" class="fa fa-crown"></i></li><li id="player-score-${element.id}" class="list-group-item" style="border-top:none; padding-top:0; margin-top:0;"><i style="font-size:9pt">${e.scores[element.id]} pont</i></li>`;
                }else{
                    if($.inArray(element.id, e.players_played) !== -1) {
                        players_html += `<li style="border-bottom:none; margin-bottom:0; padding-bottom:0; background:rgba(255,255,255,0.2);" class="list-group-item" id="player-${element.id}">${element.name} <i class="fa fa-check"></i></li><li id="player-score-${element.id}" class="list-group-item" style=" background:rgba(255,255,255,0.2); border-top:none; padding-top:0; margin-top:0;"><i style="font-size:9pt">${e.scores[element.id]} pont</i></li>`;
                    }else{
                        players_html += `<li style="border-bottom:none; margin-bottom:0; padding-bottom:0;" class="list-group-item" id="player-${element.id}">${element.name}</li><li id="player-score-${element.id}" class="list-group-item" style="border-top:none; padding-top:0; margin-top:0;"><i style="font-size:9pt">${e.scores[element.id]} pont</i></li>`;
                    }
                }
            });
            players_html += `</ul>`;

            players_div.html(players_html);

            // Show cards needed
            let cards_needed = parseInt(e.cards_needed);
            $('#cards_needed').val(cards_needed);
            if(host_user_id!=user_id){
                let answers_html = ``;
                for(let i = 0; i<cards_needed; i++) {
                    if($.inArray(user_id, e.players_played) === -1) {
                        answers_html += `<input type="hidden" id="answer-id-${i + 1}"><div class="col-xl-3"><div data-toggle="tooltip" title="${i + 1}. kártya" class="card"><div style="height:150px; background-color:rgba(255,255,255,0.05);" class="card-body" id="answer-${i + 1}"></div></div></div>`;
                    }else{
                        answers_html += `<div class="col-xl-3"><div data-toggle="tooltip" title="${i+1}. kártya" class="card"><div style="height:150px; background-color:rgba(255,255,255,0.2);" class="card-body">${e.cards_played[i]}</div></div></div>`;
                    }
                }
                answers_html += ``;
                answers_div.html(answers_html);
            }

            // Refresh tooltips
            $(function() {
                $('[data-toggle="tooltip"]').tooltip()
            });
        },
        error:function(e) {
            console.log(e);
        }
    });

    $('#submit-link').on('click', function(){
        let answers_given = parseInt($('#answers_given').val());
        let data = {
            _token: $('#_token').val()
        };
        for(let i=0; i<answers_given; i++) {
            data[`answer${i+1}`] = $(`#answer-id-${i+1}`).val();
        }

        $.ajax({
            url: $('#submit_url').val(),
            type: "POST",
            dataType: "json",
            data: data,
            success: function(e){
                console.log(e);
            },
            error: function(e){
                console.log(e);
            }
        });
    });

    $('#reset-button').on('click', function(){
        location.reload();
        // let cards_needed = $('#cards_needed').val();
        // $('#answers_given').val("");
        // for(let i = 1; i<= cards_needed; i++) {
        //     $('#answer-' + i).html("");
        //     $('#answer-id-' + i).val("");
        //     $('#answer-' + i).css('background-color','rgba(255,255,255,0.05)');
        // }
        // $('#submit-button').css('display','none');
        // $('#reset-button').css('display','none');
    });

});

function addAnswer(card_id, card_text) {
    let cards_needed = $('#cards_needed').val();
    let answers_given = $('#answers_given').val();
    let reset_button_div = $('#reset-button');
    if(answers_given === cards_needed) {
        return;
    }

    answers_given++;
    $('#answer-id-' + answers_given).val(card_id);
    $('#answer-' + answers_given).html(
        `<p>${card_text}</p>`
    );
    $('#answer-' + answers_given).css('background-color','rgba(255,255,255,0.2)');
    $('#answers_given').val(answers_given);

    if(answers_given == cards_needed) {
        $('#submit-button').css('display','block');
    }

    $('#white-card-' + card_id).html(
        `<div class="card card-white"><div class="card-body" style="background-color:rgba(255,255,255,0.3);"><p>${card_text}</p></div></div>`
    );

    reset_button_div.css('display','block');
}

let pusher = new Pusher($('#pusher').val(), {
    cluster: 'eu',
    forceTLS: true
});

let slug = $('#slug').val();

let channel = pusher.subscribe('game-' + slug);

channel.bind('turn-plays-finished', function(data) {
    if($('#user_id').val() == data.message.host_id) {
        window.location = $('#choose_winner_url').val();
    }else{
        window.location = data.message.recap_url;
    }
});

channel.bind('play-card', function(data) {
    let html = $('#player-'+ data.message).html();
    html += ` <i class="fa fa-check"></i>`;
    $('#player-' + data.message).css('background','rgba(255,255,255,0.2)');
    $('#player-' + data.message).html(html);
    if(data.message == $('#user_id').val()){
        location.reload();
    }
    $('#player-score-' + data.message).css('background','rgba(255,255,255,0.2)');
});

channel.bind('turn-finished', function(data) {
    window.location = data.message.recap_url;
});
