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

            let white_cards_html = ``;
            let players_html = `<h5 class="card-title">Játékosok</h5><ul class="list-group">`;

            // Show black card data
            let black_card_text = JSON.parse(e.black_card.text).join(" ____ ");

            black_card_div.html(
                `<div class="card-body"><h5 class="card-title">` + black_card_text + `</h5></div>`
            );

            // Show white cards
            e.cards.forEach(function(element){
                let card_text = JSON.parse(element.text).join("");
                if(host_user_id != $('#user_id').val()){
                    white_cards_html += `<div class="col-md-3"><a class="card-link" href="#" onclick="addAnswer(${element.id},'${card_text}')"><div class="card card-white"><div class="card-body">${card_text}<p></p></div></div></a></div>`;
                }else{
                    white_cards_html += `<div class="col-md-3"><div class="card card-white"><div class="card-body"><p>${card_text}</p></div></div></div>`;
                }
            });
            white_cards_div.html(white_cards_html);

            // Show players
            e.players.forEach(function(element){
                if(element.id===host_user_id){
                    players_html += `<li class="list-group-item" id="player-${element.id}">${element.name} <i data-toggle="tooltip" title="Ő választ nyertest" class="fa fa-crown"></i></li>`;
                }else{
                    players_html += `<li class="list-group-item" id="player-${element.id}">${element.name}</li>`;
                }
            });
            players_html += `</ul>`;

            players_div.html(players_html);

            // Show cards needed
            let cards_needed = parseInt(e.cards_needed);
            $('#cards_needed').val(cards_needed);
            if(host_user_id!=$('#user_id').val()){
                let answers_html = ``;
                for(let i = 0; i<cards_needed; i++) {
                    answers_html += `<input type="hidden" id="answer-id-${i+1}"><div class="col-md-3"><div data-toggle="tooltip" title="${i+1}. kártya" class="card"><div style="height:150px; background-color:rgba(255,255,255,0.05);" class="card-body" id="answer-${i+1}"></div></div></div>`;
                }
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
            url: $('#submit-url').val(),
            type: "POST",
            dataType: "json",
            data: data
        });
    });
});

function addAnswer(card_id, card_text) {
    let cards_needed = $('#cards_needed').val();
    let answers_given = $('#answers_given').val();
    if(answers_given === cards_needed) {
        return;
    }

    answers_given++;
    $('#answer-id-' + answers_given).val(card_id);
    $('#answer-' + answers_given).html(
        `<p>${card_text}</p><button style="color:white;" class="close" onclick="removeAnswer(${answers_given})" type="button">&times;</button>`
    );
    $('#answers_given').val(answers_given);

    if(answers_given == cards_needed) {
        $('#submit-button').css('display','block');
    }
}

function removeAnswer(answer_id) {
    let answers_given = $('#answers_given').val();
    $('#answer-' + answer_id).html(``);
    $('#answers_given').val(answers_given-1);
    if(answers_given != cards_needed) {
        $('#submit-button').css('display','none');
    }
}
