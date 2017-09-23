<?php

define('DS', DIRECTORY_SEPARATOR);
define('BASE_DIR', __DIR__);
define('GAMES_DIR', BASE_DIR.DS.'games');

if(isset($_GET['start']) && isset($_GET['player'])) {
    $gamestat = array(
        'angel' => rand(1, -1) * pi()/18,
        'players' => array(
            $_GET['player'] => array(
                'score' => 0,
                'x'     => 0,
            ),
        ),
    );
    $filename = 'start__'.md5($_GET['player']).'__'.time();
    file_put_contents(GAMES_DIR.DS.$filename, json_encode($gamestat));
    echo json_encode(array('success' => true, 'game' => $filename));
}

if(isset($_GET['find']) && isset($_GET['player'])) {
    echo json_encode(array('success' => true, 'games' => glob('start_*')));
}

if(isset($_GET['join']) && isset($_GET['game']) && isset($_GET['player'])) {
    $gamestat = file_get_contents(GAMES_DIR.DS.$_GET['game']);
    $gamestat = json_decode($gamestat, true);
    $gamestat['players'][$_GET['player']]['score'] = 0;
    $gamestat['players'][$_GET['player']]['x'] = 0;
    unlink(GAMES_DIR.DS.$_GET['game']);
    $id = key($gamestat['players']);
    $name = 'game__'.md5($id).'__'.md5($_GET['player']).'__'.time();
    file_put_contents(GAMES_DIR.DS.$name, json_encode($gamestat));
    echo json_encode(array('success' => true, 'game' => $name));
}

if(isset($_GET['playing']) && isset($_GET['game'])) {
    if(is_readable(GAMES_DIR.DS.$_GET['name'])) {
        $gamestat = file_get_contents(GAMES_DIR.DS.$_GET['game']);
        $gamestat = json_decode($gamestat, true);
        if(isset($_GET['hit']))
            $gamestat['angel'] = rand(1, -1)*pi()/18;
        if(isset($_GET['win']) && isset($_GET['player']))
            $gamestat['players'][$_GET['player']]['score']++;
        if(isset($_GET['player']) && isset($_GET['x']))
            $gamestat['players'][$_GET['player']]['x'] = $_GET['x'];
        foreach($gamestat['players'] as $id => $player) {
            if($id != $_GET['player']) {
                $x = $players['x'];
                break;
            }
        }

        echo json_encode(array('success' => true, 'angel' => $gamestat['angel'], 'x' => $x));
        unlink(GAMES_DIR.DS.$_GET['game']);
        file_put_contents(GAMES_DIR.DS.$_GET['game'], json_encode($gamestat));
    } else {
        echo json_encode(array('success' => false));
    }
}

/*
define('DS', DIRECTORY_SEPARATOR);
define('BASE_DIR', __DIR__);
define('CLASS_DIR', BASE_DIR.DS.'classes');
define('LOG_DIR', BASE_DIR.DS.'logs');

function __autoload($class) {
    if(is_readable(CLASS_DIR.DS.$class.'.php'))
        require_once(CLASS_DIR.DS.$class.'.php');
}

function array_get(array $array, $key, $default = null) {
    if(isset($array[$key]))
        return $array[$key];
    return $default;
}

if(($_GET['user'] == 'mykola' && $_GET['password'] == 'pass1') || ($_GET['user'] == 'koryun' && $_GET['password'] == 'pass2')) {
    $obj = file_get_contents(BSE_DIR.DS.'current_state');
    $obj = json_decode($obj, true);
    if($_GET['user'] == 'mykola') {
        $obj['player1']['y'] = array_get($_POST, 'x', $obj['player1']['y']);
        $obj['player1']
    }
}

//require_once(BASE_DIR.DS.'engine.php');

//Engine::run();

?>