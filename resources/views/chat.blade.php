<div class="chat-box" id="chat-box">
    <div class="chat-title" id="chat-title">
        Chat
    </div>
    <div class="chat-messages" id="chat-messages"></div>
    <div class="chat-form">
        <div class="form-inline">
            <input style="width:100%;" type="text" class="form-control mr-2" id="chat-text" placeholder="Írj valamit...">
        </div>
    </div>
</div>
<input type="hidden" id="chat-url" value="{{ route('game.chat.send', ['slug' => $game->slug]) }}">
<input type="hidden" id="chat-messages-url" value="{{ route('game.chat.get', ['slug' => $game->slug]) }}">
