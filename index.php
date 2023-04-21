<?php
session_start();


/*** データの初期化 ***/
$grids = 8; 
$board = [];
for ($i = 0; $i < $grids; $i++) {
    for ($j = 0; $j < $grids; $j++) {
        $board[$i][] = null;
    }
}
$player = 'black';
$enemy = 'white';
$black = 0;
$white = 0;
$place = 0;
$directions = [];
$pot = [];


/* 初期配置 */
$board[$grids / 2 - 1][$grids / 2 - 1] = 'white'; 
$board[$grids / 2][$grids / 2] = 'white';
$board[$grids / 2][$grids / 2 -1] = 'black';
$board[$grids / 2 -1][$grids / 2] = 'black';


/*** セッションデータから上書き ***/
if (isset($_SESSION['board'])) $board = $_SESSION['board'];
if (isset($_SESSION['player'])) $player = $_SESSION['player'];
if (isset($_SESSION['enemy'])) $enemy = $_SESSION['enemy'];
if (isset($_SESSION['directions'])) $directions = $_SESSION['directions'];


/*** 盤面処理1 ***/
if ($_GET) {

    // スキップ時は処理しない
    if( isset($_GET['y']) && isset($_GET['x']) ) {

        // 置いた石
        $board[$_GET['y']][$_GET['x']] = $player;

        // 石をひっくり返す
        foreach ($directions[$_GET['y']][$_GET['x']] as $direction) {
            switch ($direction) {
        
                // 上
                case 'top':
                    $i = 1;
                    while ($board[$_GET['y'] - $i][$_GET['x']] === $enemy) {
                        $board[$_GET['y'] - $i][$_GET['x']] = $player;
                        $i++;
                    }
                    break;

                // 下
                case 'bottom':
                    $i = 1;
                    while ($board[$_GET['y'] + $i][$_GET['x']] === $enemy) {
                        $board[$_GET['y'] + $i][$_GET['x']] = $player;
                        $i++;
                    }
                    break;

                // 左
                case 'left':
                    $i = 1;
                    while ($board[$_GET['y']][$_GET['x'] - $i] === $enemy) {
                        $board[$_GET['y']][$_GET['x'] - $i] = $player;
                        $i++;
                    }
                    break;

                // 右
                case 'right':
                    $i = 1;
                    while ($board[$_GET['y']][$_GET['x'] + $i] === $enemy) {
                        $board[$_GET['y']][$_GET['x'] + $i] = $player;
                        $i++;
                    }
                    break;
                    
                // 左上
                case 'topleft':
                    $i = 1;
                    while ($board[$_GET['y'] - $i][$_GET['x'] - $i] === $enemy) {
                        $board[$_GET['y'] - $i][$_GET['x'] - $i] = $player;
                        $i++;
                    }
                    break;

                // 右上
                case 'topright':
                    $i = 1;
                    while ($board[$_GET['y'] - $i][$_GET['x'] + $i] === $enemy) {
                        $board[$_GET['y'] - $i][$_GET['x'] + $i] = $player;
                        $i++;
                    }
                    break;
                    
                // 左下
                case 'bottomleft':
                    $i = 1;
                    while ($board[$_GET['y'] + $i][$_GET['x'] - $i] === $enemy) {
                        $board[$_GET['y'] + $i][$_GET['x'] - $i] = $player;
                        $i++;
                    }
                    break;

                // 右下
                case 'bottomright':
                    $i = 1;
                    while ($board[$_GET['y'] + $i][$_GET['x'] + $i] === $enemy) {
                        $board[$_GET['y'] + $i][$_GET['x'] + $i] = $player;
                        $i++;
                    }
                    break;
        
            }
        }

        // ひっくり返せる方向情報初期化
        $directions = [];
        
    }
    
    // プレイヤー交代
    if ($player === 'black') {
        $player = 'white';
        $enemy = 'black';
    } else {
        $player = 'black';
        $enemy = 'white';
    };

}

/*** 盤面処理2 ***/

// 前ターンの配置可能マスを初期化
foreach ($board as $p_row_num => $p_row) {
    foreach ($p_row as $p_grid_num => $p_grid) {
        if ($p_grid === 'place') {
            $board[$p_row_num][$p_grid_num] = null;
        }
    }
}

foreach ($board as $p_row_num => $p_row) {
    foreach ($p_row as $p_grid_num => $p_grid) {

        // 石カウント
        if($p_grid === 'black') {
            $black += 1;
        }
        if($p_grid === 'white') {
            $white += 1;
        }

        // それぞれの石の周囲から配置可能か判定
        if ($p_grid === null) {
            
            // 上
            if (2 <= $p_row_num) {
                if ($board[$p_row_num - 1][$p_grid_num] === $enemy) {
                    for ($i = 2; $i <= $p_row_num; $i++) {
                        $check = $board[$p_row_num - $i][$p_grid_num];
                        if ($check === $player) {
                            $board[$p_row_num][$p_grid_num] = 'place';
                            $directions[$p_row_num][$p_grid_num][] = 'top';
                            $pot[] = '?y=' . $p_row_num . '&x=' . $p_grid_num;
                            $place += 1;
                            break;
                        } elseif ($check === 'place' || $check === null) {
                            break;
                        }
                    }
                }
            }

            // 下
            if ($p_row_num <= $grids - 3) {
                if ($board[$p_row_num + 1][$p_grid_num] === $enemy) {
                    for ($i = 2; $p_row_num + $i + 1 <= $grids; $i++) {
                        $check = $board[$p_row_num + $i][$p_grid_num];
                        if ($check === $player) {
                            $board[$p_row_num][$p_grid_num] = 'place';
                            $directions[$p_row_num][$p_grid_num][] = 'bottom';
                            $pot[] = '?y=' . $p_row_num . '&x=' . $p_grid_num;
                            $place += 1;
                            break;
                        } elseif ($check === 'place' || $check === null) {
                            break;
                        }
                    }
                }
            }

            // 左
            if (2 <= $p_grid_num) {
                if ($board[$p_row_num][$p_grid_num - 1] === $enemy) {
                    for ($i = 2; $i <= $p_grid_num; $i++) {
                        $check = $board[$p_row_num][$p_grid_num - $i];
                        if ($check === $player) {
                            $board[$p_row_num][$p_grid_num] = 'place';
                            $directions[$p_row_num][$p_grid_num][] = 'left';
                            $pot[] = '?y=' . $p_row_num . '&x=' . $p_grid_num;
                            $place += 1;
                            break;
                        } elseif ($check === 'place' || $check === null) {
                            break;
                        }
                    }
                }
            }

            // 右
            if ($p_grid_num <= $grids - 3) {
                if ($board[$p_row_num][$p_grid_num + 1] === $enemy) {
                    for ($i = 2; $p_grid_num + $i + 1 <= $grids; $i++) {
                        $check = $board[$p_row_num][$p_grid_num + $i];
                        if($check === $player) {
                            $board[$p_row_num][$p_grid_num] = 'place';
                            $directions[$p_row_num][$p_grid_num][] = 'right';
                            $pot[] = '?y=' . $p_row_num . '&x=' . $p_grid_num;
                            $place += 1;
                            break;
                        } elseif ($check === 'place' || $check === null) {
                            break;
                        }
                    }
                }
            }

            // 左上
            if (2 <= $p_row_num && 2 <= $p_grid_num) {
                if ($board[$p_row_num - 1][$p_grid_num - 1] === $enemy) {
                    $topleft_limit = $p_row_num <= $p_grid_num ? $p_row_num : $p_grid_num;
                    for ($i = 2; $i <= $topleft_limit; $i++) {
                        $check = $board[$p_row_num - $i][$p_grid_num - $i];
                        if ($check === $player) {
                            $board[$p_row_num][$p_grid_num] = 'place';
                            $directions[$p_row_num][$p_grid_num][] = 'topleft';
                            $pot[] = '?y=' . $p_row_num . '&x=' . $p_grid_num;
                            $place += 1;
                            break;
                        } elseif ($check === 'place' || $check === null) {
                            break;
                        }
                    }
                }
            }

            // 右上
            if (2 <= $p_row_num && $p_grid_num <= $grids - 3) {
                if ($board[$p_row_num - 1][$p_grid_num + 1] === $enemy) {
                    $topright_limit = $p_row_num <= $grids - $p_grid_num -1 ? $p_row_num : $grids - $p_grid_num -1;
                    for ($i = 2; $i <= $topright_limit; $i++) {
                        $check = $board[$p_row_num - $i][$p_grid_num + $i];
                        if ($check === $player) {
                            $board[$p_row_num][$p_grid_num] = 'place';
                            $directions[$p_row_num][$p_grid_num][] = 'topright';
                            $pot[] = '?y=' . $p_row_num . '&x=' . $p_grid_num;
                            $place += 1;
                            break;
                        } elseif ($check === 'place' || $check === null) {
                            break;
                        }
                    }
                }
            }

            // 左下
            if ($p_row_num <= $grids - 3 && 2 <= $p_grid_num) {
                if ($board[$p_row_num + 1][$p_grid_num - 1] == $enemy) {
                    $bottomleft_limit = $grids - $p_row_num -1 <= $p_grid_num ? $grids - $p_row_num -1 : $p_grid_num;
                    for ($i = 2; $i <= $bottomleft_limit; $i++) {
                        $check = $board[$p_row_num + $i][$p_grid_num - $i];
                        if ($check === $player) {
                            $board[$p_row_num][$p_grid_num] = 'place';
                            $directions[$p_row_num][$p_grid_num][] = 'bottomleft';
                            $pot[] = '?y=' . $p_row_num . '&x=' . $p_grid_num;
                            $place += 1;
                            break;
                        } elseif ($check === 'place' || $check === null) {
                            break;
                        }
                    }
                }
            }

            // 右下
            if ($p_row_num <= $grids - 3 && $p_grid_num <= $grids - 3) {
                if ($board[$p_row_num + 1][$p_grid_num + 1] == $enemy) {
                    $bottomright_limit = $grids - $p_row_num -1 <= $grids - $p_grid_num -1 ? $grids - $p_row_num -1 : $grids - $p_grid_num -1;
                    for ($i = 2; $i <= $bottomright_limit; $i++) {
                        $check = $board[$p_row_num + $i][$p_grid_num + $i];
                        if ($check === $player) {
                            $board[$p_row_num][$p_grid_num] = 'place';
                            $directions[$p_row_num][$p_grid_num][] = 'bottomright';
                            $pot[] = '?y=' . $p_row_num . '&x=' . $p_grid_num;
                            $place += 1;
                            break;
                        } elseif ($check === 'place' || $check === null) {
                            break;
                        }
                    }
                }
            }

        }
    }
}


/*** 更新されたデータを保存 ***/
$_SESSION['board'] = $board;
$_SESSION['player'] = $player;
$_SESSION['enemy'] = $enemy;
$_SESSION['directions'] = $directions;


if($_GET) {
    header('Location: index.php');
    exit;
}


?>
<!DOCTYPE html>
<html>
    <head>
        <title>PHP-Reversi</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <article>
            <div class="container">

                <div class="status">
                    <table>
                        <tr>
                            <!-- 黒マス数 -->
                            <td>
                                <?php echo '● ' . $black; ?>
                            </td>
                            <!-- 勝敗 -->
                            <?php if ($black + $white == $grids * $grids) : ?>
                                <?php if ($black > $white) : ?>
                                    <td class="result">黒の勝ち！</td>
                                <?php elseif ($black < $white) : ?>
                                    <td class="result">白の勝ち！</td>
                                <?php else : ?>
                                    <td class="result">引き分け</td>
                                <?php endif ; ?>
                            <!-- 現在ターン -->
                            <?php else : ?>
                                <?php if ($player == 'white') : ?>
                                    <td class="white-turn">白のターン</td>
                                <?php elseif ($player == 'black') : ?>
                                    <td class="black-turn">黒のターン</td>
                                <?php endif ; ?>
                            <?php endif ; ?>
                            </td>
                            <!-- 白マス数 -->
                            <td>
                                <?php echo '○ ' . $white; ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- 盤面 -->
                <div class="board">
                    <table>
                        <?php foreach ($board as $d_row_num => $d_row) : ?>
                            <tr>
                                <?php foreach ($d_row as $d_grid_num => $d_grid) : ?>
                                    <?php switch ($d_grid) :
                                        case null:
                                    ?>
                                            <td><span class="blank"></span></td>
                                            <?php break; ?>
                                        <?php case 'place': ?>
                                            <?php if ($player === 'black') : ?>
                                                <td><a href="?y=<?= $d_row_num ?>&x=<?= $d_grid_num ?>"><span class="blank"></span></a></td>
                                            <?php else : ?>
                                                <td><span class="blank"></span></td>
                                            <?php endif ;?>
                                            <?php break; ?>
                                        <?php case 'black': ?>
                                            <td><span class="black"></span></td>
                                            <?php break; ?>
                                        <?php case 'white': ?>
                                            <td><span class="white"></span></td>
                                            <?php break; ?>
                                    <?php endswitch ; ?>
                                <?php endforeach ; ?>
                            </tr>
                        <?php endforeach ; ?>
                    </table>
                </div>

                <!-- オプションボタン -->
                <div class="buttons">
                    <table>
                        <tr>
                            <!-- リセット -->
                            <td>
                                <form action="reset.php" method="post">
                                    <input type="hidden" name="reset" value="reset">
                                    <button type="submit">リセット</button>
                                </form>
                            </td>
                            <!-- スキップ -->
                            <td>
                                <form action="" method="get">
                                    <input type="hidden" name="skip" value="1">
                                    <button type="submit" <?php if ($black + $white == $grids * $grids || 0 < $place || $player === 'white') echo 'disabled'; ?>>スキップ</button>
                                </form>
                            </td>
                        </tr>
                    </table>
                </div>
                
            </div>
        </article>
    </body>
</html>

<?php
/*** CPU ***/
if ($player === 'white') {
    if (empty($pot)) {
        echo "<script>setTimeout(function(){location.href='?skip=1'},2000);</script>'";
    } else {
        $rand = array_rand($pot, 1);
        echo "<script>setTimeout(function(){location.href='" . $pot[$rand] . "'},1000);</script>'";
    }
}
?>