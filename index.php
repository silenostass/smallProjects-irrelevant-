
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Числобуквенник</title>
    </head>
    <body>
        <div class="">
            <form class="" method="GET">
                <input type="number" name="number" placeholder="Введи кол-во рублей">
            </form>
        </div>
    </body>
</html>

<?php //Программа переводит числа от 0 до 999 999 999 999 в пропись.
mb_internal_encoding('utf-8');

$number = $_GET['number'];
$zerogexp = "/^([0]+)([\d]+)/u";
if (preg_match($zerogexp, $number)) {
    $number = preg_replace($zerogexp, '$2', $number);
}
echo 'Ты выиграл ' . bigNumberToText($number);


function inclineWord($number) { //склонение рубля
    $last2Digits = $number % 100;
    $regexp1 = "/^([2-9]?1$)/u";
    $regexp2 = "/^[2-9]?[2-4]$/u";
    if (preg_match($regexp1, $last2Digits)) {
        $word =  "($number)  рубль.";
    } elseif (preg_match($regexp2, $last2Digits)) {
        $word =  "($number)  рубля.";
    } else {
        $word =  "($number)  рублей.";
    }
    return $word;
}

function smallNumberToText($number) { //переводит трёхзначные в слова
    $spelling = array(
                                                            10  =>  'десять',       100 =>  'сто',
        1   =>  'один',         11  =>  'одиннадцать',      20  =>  'двадцать',     200 =>  'двести',
        2   =>  'два',          12  =>  'двенадцать',       30  =>  'тридцать',     300 =>  'триста',
        3   =>  'три',          13  =>  'тринадцать',       40  =>  'сорок',        400 =>  'четыреста',
        4   =>  'четыре',       14  =>  'четырнадцать',     50  =>  'пятьдесят',    500 =>  'пятьсот',
        5   =>  'пять',         15  =>  'пятнадцать',       60  =>  'шестьдесят',   600 =>  'шестьсот',
        6   =>  'шесть',        16  =>  'шестнадцать',      70  =>  'семьдесят',    700 =>  'семьсот',
        7   =>  'семь',         17  =>  'семнадцать',       80  =>  'восемьдесят',   800 =>  'восемьсот',
        8   =>  'восемь',       18  =>  'восемнадцать',     90  =>  'девяносто',     900 =>  'девятьсот',
        9   =>  'девять',       19  =>  'девятнадцать'
    );
    $cofForValue = 100;
    $last3Digits = $number % 1000;
    if (mb_strlen($last3Digits) == 2 ) {
        $last3Digits = '0' . $last3Digits;
    }
    if (mb_strlen($last3Digits) == 1 ) {
        $last3Digits = '00' . $last3Digits;
    }
    $smallNumbArr = str_split($last3Digits, 1);
    if ($smallNumbArr[1] == 1 && $smallNumbArr[2] != 0) {
        $smallNumbArr[1] = '1' . $smallNumbArr[2];
        unset($smallNumbArr[2]);
    }
    foreach ($smallNumbArr as $key => $value) {
        if ($value < 11 || $value > 19) {
            $value *= $cofForValue;
        }
        $smallNumbArr[$key] = $spelling[$value];
        $cofForValue /= 10;
        $smallNumberWord = implode(" ", $smallNumbArr);
    }
    return $smallNumberWord;
}

function bigNumberToText($number) { //основная функция, внутри задействованы все остальные
    $rub = inclineWord($number);
    if ($number == 0) {
        $bigNumberWord = 'ноль' . ' ' . $rub;
        return $bigNumberWord;
    }
    $numberNames = array(
        1 => 'тысяч',
        2 => 'миллионов',
        3 => 'миллиардов'
    );
    $bigNumberArr = array();
    for ($i = 0; mb_strlen($number) >= 1; $number = mb_substr($number, 0, -3), $i++) {
        $smallNumberWord = smallNumberToText($number);
        if (mb_substr($number, -3) == '000') {
            $bigNumberArr[$i] = $smallNumberWord;
            continue;
        }
        if (!empty(formBigNumber($smallNumberWord, $i)[1])) {
            $numberNames[$i] = formBigNumber($smallNumberWord, $i)[1];
        }
        $smallNumberWord = formBigNumber($smallNumberWord, $i)[0];
        $bigNumberArr[$i] = $smallNumberWord . ' ' . $numberNames[$i];
    }
    $bigNumberArr = array_reverse($bigNumberArr);
    $bigNumberWord = implode(" ", $bigNumberArr) . ' ' . $rub;
    return $bigNumberWord;
}

function formBigNumber($smallNumberWord, $i) { //склонение слов, добавить новые разряды
    if (mb_strpos($smallNumberWord, ' один') == true && $i == 1) {
        $smallNumberWord = ' одна';
        $numberNames[$i] = 'тысяча';
    }
    if (mb_strpos($smallNumberWord, ' два') == true && $i == 1) {
        $smallNumberWord = ' две';
    }
    if (  ( (mb_strpos($smallNumberWord, ' две') == true) | (mb_strpos($smallNumberWord, ' три') == true) | (mb_strpos($smallNumberWord, ' четыре') == true )) && $i == 1) {
        $numberNames[$i] = 'тысячи';
    }
    if (mb_strpos($smallNumberWord, ' один') == true && $i == 2) {
        $numberNames[$i] = 'миллион';
    }
    if (  ( (mb_strpos($smallNumberWord, ' два') == true) | (mb_strpos($smallNumberWord, ' три') == true) | (mb_strpos($smallNumberWord, ' четыре') == true )) && $i == 2) {
        $numberNames[$i] = 'миллиона';
    }
    if (mb_strpos($smallNumberWord, ' один') == true && $i == 3) {
        $numberNames[$i] = 'миллиард';
    }
    if (  ( (mb_strpos($smallNumberWord, ' два') == true) | (mb_strpos($smallNumberWord, ' три') == true) | (mb_strpos($smallNumberWord, ' четыре') == true )) && $i == 3) {
        $numberNames[$i] = 'миллиарда';
    }
    return [$smallNumberWord, $numberNames[$i]];
}
?>
