<?php

/***********************
 * ROUTES EXAMPLES
 ************************/

/**
 * Login 
 */
function login ($args) 
{
    global $_;

    lan($args);

    if (!empty($args['rent'])) {
        $_SESSION['secret'] = $args['rent'];
    }
    else {
        header('Location: /logout');
    }

    $results = [
        "OUTPUT"       => $_("inject: app/assets/login.html"),
        "MAIN_SCRIPT"  => $_("inject: app/assets/login.js"),
        "ZIPVALUE"     => "33160"
    ];
    return $results;
    
}

/**
 * Pay page 
 */
function pay ($args) 
{
    global $_;

    if (!empty($args['zip']) && !empty($_SESSION['secret'])) {
        $rentArray = $_("assoc: SELECT * FROM rent WHERE secret = ? AND zip = ? AND status = 'active'", [$_SESSION['secret'], $args['zip']]);
        if (empty($rentArray)) {
            header('Location: /login');    
        }

        $renterArray = $_("assoc: SELECT * FROM renter WHERE id = ? AND status = 'active'", [$rentArray['renter']]);
        if (empty($renterArray)) {
            header('Location: /login');    
        }
        
        $cardArray = $_("assoc: SELECT * FROM cards WHERE renter = ? AND status = 'active'", [$renterArray['id']]);
        if ($cardArray) {
            $cardBtn = "<:CHANGECARD/>";
            $payBtn = '<a class="button button-primary" href="/dashboard"><:PAYBTNTEXT/></a>';
        }
        else {
            $cardBtn = "<:ADDCARD/>";
            $payBtn = "";
        }
        $_SESSION['rent'] = $rentArray;
        $_SESSION['renter'] = $renterArray;
        $_SESSION['card'] = $cardArray;
    }
    else {
        header('Location: /logout');
        die();
    }

    $results = [
        "OUTPUT"       => $_("inject: app/assets/pay.html"),
        "MAIN_SCRIPT"  => $_("inject: app/assets/pay.js"),
        "AMOUNT"       => $rentArray['amount'],
        "CARD"         => $cardBtn,
        "PAYBTN"       => $payBtn
    ];
    return $results;
}

/**
 * Addcard page 
 */
function addCard ($args) 
{
    global $_;

    $cardResult = "";

    if (!empty($args['name']) && !empty($args['card']) &&
        !empty($args['cvv']) && !empty($args['my'])) {
        $_(": UPDATE cards SET status = 'deleted' WHERE renter = ?", [$_SESSION['renter']['id']]);
        if ($cardId = $_("insertid: INSERT INTO cards (renter, token) VALUES (?,?)", [$_SESSION['renter']['id'], $args['card']])) {
            $cardArray = $_("assoc: SELECT * FROM cards WHERE id = ? AND status = 'active'", [$cardId]);
            if (empty($cardArray)) {
                header('Location: /pay');
                die();    
            }
            $_SESSION['card'] = $cardArray;
            header('Location: /dashboard');
            die();
        }
        else {
            $cardResult = ($_SESSION['LANGUAGE_IN_USE'] == "es") ? 
                           "Vuelva a intentarlo, por favor" : "Try again, please";
        }
    }

    $results = [
        "OUTPUT"       => $_("inject: app/assets/addcard.html"),
        "MAIN_SCRIPT"  => $_("inject: app/assets/addcards.js"),
        "CARD_RESULT"  => $cardResult
    ];
    return $results;
}

/**
 * Dashboard page 
 */
function dashboard ($args) 
{
    global $_;

    if (!empty($_SESSION['paid'])) {
        $dashboard = "<:THANK_YOU/>";
    }
    else {
        $dashboard = "<:NO_NEED/>";
    }

    $table = '<table class="u-full-width"><thead><tr><th>Amount</th><th>Date</th></tr></thead><tbody>';
    $receipts = $_("assoclist: SELECT * FROM receipts ORDER BY created DESC");
    if ($receipts) {
        foreach ($receipts as $receipt) {
            $table .= "<tr>";
            $table .= "<td>".$receipt['amount']."</td>";
            $table .= "<td>".date("m/d/Y", strtotime($receipt['created']))."</td>";
            $table .= "</tr>";
        }
    }
    $table .= '</tbody></table>';

    $_SESSION['paid'] = true;

    $results = [
        "OUTPUT"           => $_("inject: app/assets/dashboard.html"),
        "DASHBOARD_RESULT" => $dashboard,
        "RECEIPTS_RESULT"  => $table
    ];
    return $results;
}

/**
 * Contact page
 */
function contact ($args) 
{
    global $_;

    $recaptcha = $_("getConfig: recaptcha");

    if (!empty($args['g-recaptcha-response']) && !empty($args['email']) && 
        !empty($args['name']) && !empty($args['subject']) && !empty($args['message'])) 
    {
        $output = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".
                                $recaptcha['secretKey'] . "&response=" . $args['g-recaptcha-response']), true);
        if (isset($output['success']) && $output['success'] == true) {
            $emailResult = $_("email: ", 
                [
                    "emailto"=> "contact@eldiletante.com", 
                    "subject" => $args['subject'], 
                    "emailfrom" => $args['email'],
                    "namefrom" => $args['name']
                ], 
                [
                    "OUTPUT" => $args['message'] . "<br>Origin: DollarLib" 
                ]
            );
            $emailMsg = $emailResult ? "EMAIL_MSG" : "EMAIL_ERROR";  
        }
    }

    $results = [
        "OUTPUT"       => $_("inject: app/assets/contact.html"),
        "MAIN_SCRIPT"  => $_("inject: app/assets/contact.js"),
        "SITE_KEY"     => $recaptcha['siteKey'],
        "EMAIL_RESULT" => isset($emailMsg) ? $_("getlang: {$emailMsg}") : ""
    ];
    return $results;
}

/**
 * Snippets page
 */
function snippets($args) 
{
    global $_;

    $results = [
        "OUTPUT"       => $_("inject: app/assets/snippets.html")
    ];
    return $results;
}
