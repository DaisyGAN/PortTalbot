<?php

    $token = '<BOT KEY>';
    $j = json_decode(file_get_contents("php://input"));

    function appendFileUnique($fp, $line)
    {
        $data = file_get_contents($fp);
        if(strpos($data, $line . "\n") === false)
            file_put_contents($fp, $line . "\n", FILE_APPEND | LOCK_EX);
    }

    if(isset($j->{'message'}->{'text'}))
    {
        if(strpos($j->{'message'}->{'text'}, "/quote") !== FALSE)
        {
            $file = file("out.txt"); 
            $line = $file[rand(0, count($file) - 1)];
            $chatid = $j->{'message'}->{'chat'}->{'id'};
            file_get_contents("https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chatid . "&text=".urlencode($line));
            http_response_code(200);
            exit;
        }

        $msg = $j->{'message'}->{'text'};
        $ss = preg_replace("/[^a-z@ ]/", '', strtolower($msg));
        //$ss = preg_replace("\b[a-z]{1,2}\b", '', $ss);
        $pp = explode(' ', $ss);
        $pps = array_slice($pp, 0, 16);

        $str = "";
        foreach($pps as $p)
            if(strlen($p) <= 16 && $p != "" && $p != " ")
                $str .= $p . " ";
        $str = rtrim($str, ' ');

        appendFileUnique("tgmsg.txt", substr($str, 0, 256));

        foreach($pp as $p)
            if(strlen($p) <= 16)
                appendFileUnique("tgdict.txt", substr($p, 0, 16));
    }

    http_response_code(200);

?>
