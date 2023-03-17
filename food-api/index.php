<?php

require_once '../../config.food.php';

// Stranka, ktoru chcem parsovat.
$freefoodURL = "http://www.freefood.sk/menu/#fayn-food";

function getPageContent($db, $url, $name) {
    // Funkcia ktora pomocou cURL ulozi stranku definovanu v $url
    // a ulozi do databazy pod nazvom $name.

    // cURL inicializacia
    $ch = curl_init();

    // Konfiguracia cURL: zadam stranku, ktoru chcem parsovat a navratovy typ -> 1=string.
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Vykonanie cURL dopytu.
    $output = curl_exec($ch);

    // Slusne ukoncim a uvolnim cURL.
    curl_close($ch);

    // Vlozim obsah stranky do databazy.
    $sql = "INSERT INTO sites (name, html) VALUES (:name, :html)";
    $stmt = $db->prepare($sql);

    $stmt->bindParam(":name", $name, PDO::PARAM_STR);
    $stmt->bindParam(":html", $output, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo "Stranka ulozena.";
    } else {
        echo "Ups. Nieco sa pokazilo";
    }

    unset($stmt);
}

function getMenuFromDB($db, $name) {
    // Funkcia ziska html obsah z databazy.
    $page_content = "";
    $sql = "SELECT html FROM sites WHERE name = :name LIMIT 1";

    $stmt = $db->prepare($sql);

    $stmt->bindParam(":name", $name, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        // Uzivatel existuje, skontroluj heslo.
        $row = $stmt->fetch();
        $page_content = $row["html"];
    } else {
        echo "Nenachadza sa v tabulke alebo je duplicitne.";
    }

    return $page_content;

}


// getPageContent($pdo, $freefoodURL, "free-food");
$output = getMenuFromDB($pdo, "free-food");

// Parsovanie pomocou DOMDocument metod.
$dom = new DOMDocument();
$dom->loadHTML($output);

// Ziskam cast stranky s menu, ktore ma zaujima - to je <div> s id="fayn-food"
$freeFoodPart = $dom->getElementById("fayn-food");
// V tomto <div> elemente su:
// DOMText
// ::before
// <h3...>
// <div...>  --> tento div ma zaujima, v nom je <ul> s dennym menu.
// <div...>
// ::after
// DOMText
// Kto neveri, nech si v cykle vypise typ prvkov v poli $freeFoodPart...

// Hladam element <ul>. Ten je v liste potomkov=childNodes v <div> elemente, ktory je 3. v poradi.
// V tomto <div> elemente sa <ul> nachadza na konci, mozem k nemu pristupit ako k $lastElementChild.
$menuList = $freeFoodPart->childNodes[3]->lastElementChild;

// S vyuzitim dedicnosti ziskam pristup ku dnom v tyzdni.
$mon = $menuList->firstElementChild;


$tue = $mon->nextElementSibling;
$wen = $tue->nextElementSibling;
$thu = $wen->nextElementSibling;
$fri = $menuList->lastElementChild;

// Viem si tak vyskladat napr. asociativne pole, reprezentujuce cely tyzden.
$week = array(
    'mon' => $mon,
    'tue' => $tue,
    'wen' => $wen,
    'thu' => $thu,
    'fri' => $fri
    );

// Zoberiem si napr. pondelok. Najprv potrebujem vytiahnut datum.
$date_day = $mon->firstElementChild->textContent;

// Vytiahnem si zoznam jedal z pondelka. Je to posledny childElement - <ul> s class="day-offer"
$monday_menu = $mon->lastElementChild;

// Polievka
$polievka = $monday_menu->firstElementChild;
$monday_menu->removeChild($polievka);

// Jednotlive menu polozky. Kazdu postupne odstranim.
$menu1 = $monday_menu->firstElementChild;
$monday_menu->removeChild($menu1);

$menu2 = $monday_menu->firstElementChild;
$monday_menu->removeChild($menu2);

$menu3 = $monday_menu->firstElementChild;
$monday_menu->removeChild($menu3);

// Ak chcem este z menu zobrat cenu a typ:
$brand = $menu3->getElementsByTagName('span')[0];
$price = $menu3->getElementsByTagName('span')[1];
$menu3->removeChild($brand);
$menu3->removeChild($price);

// Nakoniec vypis
echo "Den: " . $date_day . '<br>';
echo "Polievka: " . $polievka->textContent . '<br>';
echo "Menu 1: " . $menu1->textContent . '<br>';
echo "Menu 2: " . $menu2->textContent . '<br>';
echo "Menu cislo " . $brand->textContent .": " . $menu3->textContent . " -- Cena: " . $price->textContent . '<hr>';
echo '<hr>';
// Parsovanie pomocou DOMXPath

$dom->loadHTML($output);
$xpath = new DOMXPath($dom);

// Pomocou xpath viem ziskat aj elementy podla atributov a teda aj podla triedy
$menu_lists = $xpath->query('//ul[contains(@class, "daily-offer")]');
// Stranka poskytuje menu pre 3 restauracie teda su tam aj 3x daily-offer zoznamy.
$fayn_food = $menu_lists[1];

foreach ($fayn_food->childNodes as $day) {
    // Nezaujima ma DOMText, iba prvok typu DOMElement.
    if ($day->nodeType === XML_ELEMENT_NODE) {
        // Ziskam si datum a rozdelim ho na den a datum, kedze tieto dva su oddelene ciarkou.
        $datum = explode(',', $day->firstElementChild->textContent);
        echo "Den: " . $datum[0] . " Datum: " . trim($datum[1]);
        echo '<br>';

        // Iterujem cez ponuku dna.
        foreach ($day->lastElementChild->childNodes as $ponuka) {
            // Ziskam si poradove cislo jedla, resp. pismeno P. urcuje polievku
            $typ = $ponuka->firstElementChild;
            $cena = $ponuka->lastElementChild;

            // Odstranim typ a cenu aby mi ostal iba text ponuky.
            $ponuka->removeChild($typ); // Vymazanie por. cisla
            $ponuka->removeChild($cena); // Vymazanie ceny

            echo "Typ: " . $typ->textContent . ' Jedlo: ' . $ponuka->textContent . ' Cena: ' . $cena->textContent;
            echo '<br>';
        }
        echo '<hr>';
    }
}




