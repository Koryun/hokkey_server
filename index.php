<?php

define('DS', DIRECTORY_SEPARATOR);
define('BASE_DIR', __DIR__);
define('GAMES_DIR', BASE_DIR.DS.'games');

function check() {
    foreach(func_get_args() as $arg) {
        if(!isset($_GET[$arg]))
            return false;
    }
    return true;
}

function get($name) {
    return $_GET[$name];
}

function response($result) {
    $result['success'] = true;
    echo json_encode($result)."\n\r";
}

function report($error) {
    echo json_encode(['success' => false, 'error' => $error]);
}

$_GET['player'] = 1;
$_GET['playing'] = true;
$_GET['game'] = 'game__1__2__201709260451';
$_GET['position'] = 1;

if(check('player') && in_array(get('player'), [1, 2])) {
    if(check('start')) {
        $id = get('player');
        $gamestat = [
            'angel'     => rand(1, -1) * pi()/18,
            'players'   => [
                $id     => [
                    'score' => 0,
                    'position'     => 0,
                ],
            ],
        ];
        $filename = 'start__'.$id.'__NULL__'.date('YmdHi', time());
        $success = file_put_contents(GAMES_DIR.DS.$filename, json_encode($gamestat));
        if($success)
            response([]);
        else
            report('could not write file');
    } elseif(check('find')) {
        $time = date('YmdHi', time());
        $mintime = $time - 5;
        $result = glob(GAMES_DIR.DS."start__*__NULL__*");
        array_walk($result, function(&$item, $key) {
            $item = explode('__', $item);
            if($item[1] != get('player'))
                $item = $item[1];
            else
                $item = 0;         
        });
        response(['result' => $result]);
    } elseif(check('join', 'game')) {
        $time = date('YmdHi', time());
        $mintime = $time - 5;
        $pid = get('game');
        $games = glob(GAMES_DIR.DS.'start__'.$pid."__NULL__*");
        if(!empty($games)) {
            $game = array_shift($games);
            $gamestat = file_get_contents($game);
            $gamestat = json_decode($gamestat, true);
            $gamestat['players'][get('player')]['score'] = 0;
            $gamestat['players'][get('player')]['position'] = 0;
            $players = array_keys($gamestat['players']);
            unlink($game);
            $name = 'game__'.$players[0].'__'.$players[1].'__'.date('YmdHi', time());
            $success = file_put_contents(GAMES_DIR.DS.$name, json_encode($gamestat));
            if($success)
                response([]);
            else
                report('could not write file');
        } else
            report('no file found');
    } elseif(check('check')) {
        $games = glob(GAMES_DIR.DS.'game__'.get('player').'__*__*');
        if(empty($games))
            $games = glob(GAMES_DIR.DS.'game__*__'.get('player').'__*');
        if(!empty($games)) {
            $game = array_shift($games);
            response(['game' => str_replace(GAMES_DIR.DS, '', $game)]);
        } else
            report('no file');
    } elseif(check('playing', 'game', 'position')) {
        if(is_readable(GAMES_DIR.DS.get('game'))) {
            $gamestat = file_get_contents(GAMES_DIR.DS.get('game'));
            $gamestat = json_decode($gamestat, true);
            $gamestat['players'][get('player')]['position'] = get('position');
            if(check('hit'))
                $gamestat['angel'] = rand(1, -1) * pi()/18;
            if(check('win'))
                $gamestat['players'][get('player')]['score']++;
            $gamestat = json_encode($gamestat);
            response(['gamestat' => $gamestat]);
            unlink(GAMES_DIR.DS.get('game'));
            file_put_contents(GAMES_DIR.DS.get('game'), $gamestat);
        } else 
            report('no file');
    }
}

?>