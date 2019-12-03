<?php
//Action arguments should be (Ratchet\ConnectionInterface $conn,$mess,App\Http\Controllers\WebSocketController $obj)

Message::type('message','GlobalMessageHandler');
Message::type('changeMuted','ChangeMutedHandler');
Message::type('changeBanned','ChangeBannedHandler');
Message::type('getUserList','GetUserListHandler');
